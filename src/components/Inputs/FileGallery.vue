<script setup>
import {onMounted, reactive} from "vue";
import AlertModal from "@components/Modals/AlertModal.vue";
import DefaultIconFile from "@assets/icons/default.svg";
import PdfIconFile from "@assets/icons/pdf.svg";
import DocIconFile from "@assets/icons/doc.svg";
import XlsIconFile from "@assets/icons/xls.svg";
import ZipIconFile from "@assets/icons/zip.svg";

const props = defineProps({
  sending: {
    type: Boolean,
    required: false,
    default: false
  },
  items: {
    type: Array,
    required: false,
    default: []
  },
  ocrItems: {
    type: Array,
    required: false,
    default: []
  },
  maxFileSize: {
    type: Number,
    required: false,
    default: 5
  },
  accept: {
    type: String,
    required: false,
    default: ""
  },
  multiple: {
    type: Boolean,
    required: false,
    default: true
  },
  withoutSelect: {
    type: Boolean,
    required: false,
    default: false
  }
});
const emit = defineEmits(['onSelect', 'onDelete']);

const url = import.meta.env.VITE_FILE_URL;
const state = reactive({
  items: props.items || [],
  ocrFiles: [],
  uploadFiles: [],
  modal: false,
  fileIdToDelete: null
});

const getFileSize = (sizeInBite) => {
  const sizeInKB = sizeInBite / 1024;
  const sizeInMB = sizeInKB / 1024;
  const sizeInGB = sizeInMB / 1024;

  if (sizeInGB > 1) {
    return `${sizeInGB.toFixed(2)} GB`;
  }
  if (sizeInMB > 1) {
    return `${sizeInMB.toFixed(2)} MB`;
  }
  if (sizeInKB > 1) {
    return `${sizeInKB.toFixed(2)} KB`;
  }
  return `${sizeInBite} B`;
}
const downloadFile = (item) => {
  window.open(url + item.file_path);
}
const checkIsImage = (extension) => {
  if (!extension)
    return false;

  return ['jpg', 'jpeg', 'png', 'gif'].includes(extension.toLowerCase());
}
const getIconByExtension = (extension) => {
  switch (extension) {
    case 'pdf':
      return PdfIconFile;
    case 'docx':
    case 'doc':
      return DocIconFile;
    case 'xlsx':
    case 'xls':
      return XlsIconFile;
    case 'zip':
    case 'rar':
      return ZipIconFile;
    default:
      return DefaultIconFile;
  }
}
const readFileAsDataURL = async (file) => {
  return await new Promise((resolve) => {
    let fileReader = new FileReader();
    fileReader.onload = (e) => resolve(fileReader.result);
    fileReader.readAsDataURL(file);
  });
}

const onFileSelect = async (data) => {
  emit('onSelect', data);
}
const onSelectedFileDelete = async (data) => {
  emit('onSelect', data);
}
const onDelete = (data) => {
  state.fileIdToDelete = data.id;
  state.modal = true;
}
const onDeleteConfirm = () => {
  props.items.map((item) => {
    if (item.id === state.fileIdToDelete) {
      item.deleted = 1;
    }

    return item;
  });

  state.modal = false;
  emit('onDelete', state.fileIdToDelete);
  state.fileIdToDelete = null;
}

onMounted(async () => {
  if(props.ocrItems.length > 0){
    const ocrItem = props.ocrItems[0];
    const file = await readFileAsDataURL(ocrItem);

    state.ocrFiles = [{
      name: ocrItem.name,
      src: file,
      size: ocrItem.size,
      type: ocrItem.type,
    }];
  }
});
</script>

<template>
  <FileUpload v-if="!withoutSelect" custom-upload :multiple="props.multiple" @select="onFileSelect($event)"
              v-model="state.uploadFiles"
              :show-upload-button="false" :show-cancel-button="false"
              invalid-file-size-message="Файл превышает установленный лимит по размеру."
              invalid-file-limit-message="Файл превышает установленный лимит по кол-ву."
              @remove="onSelectedFileDelete"
              :accept="props.accept" :maxFileSize="props.maxFileSize * 1000000">
    <template #empty>
      <span>Перетащите {{ props.multiple ? "файлы" : "файл" }} для загрузки. Максимальный размер {{ props.maxFileSize }} Мб.</span>
    </template>
  </FileUpload>

  <div v-if="state.ocrFiles.length > 0" class="mt-4">
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Распознанный файл для загрузки:</label>
    <div class="flex flex-col min-w-70 min-h-70 bg-white dark:bg-gray-700 drop-shadow hover:drop-shadow-lg rounded-md">
      <img :src="state.ocrFiles[0].src" :alt="state.ocrFiles[0].name" class="w-full h-auto rounded-tl-md rounded-tr-md"/>
      <div class="flex flex-col px-3 py-2 w-full">
        <div class="flex flex-auto justify-between">
          <div>
            <div class="title-text">{{ state.ocrFiles[0].name }}</div>
            <p class="mt-0 mb-3 text-600">{{ state.ocrFiles[0].type.toUpperCase() }} - {{
                getFileSize(state.ocrFiles[0].size)
              }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="flex flex-wrap grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-10 mt-4">
    <div v-for="item in items"
         class="flex flex-col min-w-70 min-h-70 bg-white dark:bg-gray-700 drop-shadow hover:drop-shadow-lg rounded-md">
      <img
          v-if="checkIsImage(item.file_extension)"
          :alt="item.file_name"
          :src="url + item.file_path"
          :class="'h-42.5 object-cover rounded-tl-md rounded-tr-md' + (item.deleted === 1? ' opacity-35 cursor-not-allowed' : '')"
      />
      <div
          v-if="!checkIsImage(item.file_extension)"
          :class="'h-42.5 bg-gray-200 dark:bg-gray-600 rounded-tl-md rounded-tr-md flex justify-center' + (item.deleted === 1? ' opacity-35 cursor-not-allowed' : '')"
      >
        <component class="h-full w-full p-4 text-gray-400" :is="getIconByExtension(item.file_extension)"/>
      </div>
      <div class="flex flex-col px-3 py-2 w-full">
        <div class="flex flex-auto justify-between">
          <div>
            <div class="title-text">{{ item.file_name }}</div>
            <p class="mt-0 mb-3 text-600">{{ item.file_extension.toUpperCase() }} - {{
                getFileSize(item.file_size)
              }}</p>
          </div>
        </div>
        <div class="flex gap-2 justify-end">
          <Button @click="downloadFile(item)" icon="pi pi-download" class="flex" :disabled="item.deleted === 1"/>
          <Button @click="onDelete(item)" icon="pi pi-trash" class="flex" severity="secondary"
                  :disabled="item.deleted === 1"/>
        </div>
      </div>
    </div>
  </div>
  <AlertModal
      target="#modal2"
      :isOpen="state.modal"
      @close="state.modal = false"
      @accept="onDeleteConfirm"
      title="Вы действительно хотите удалить?"
      withButtons info/>
</template>

<style scoped>
.title-text {
  @apply text-xl font-medium mb-2;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  width: 200px;
}
</style>