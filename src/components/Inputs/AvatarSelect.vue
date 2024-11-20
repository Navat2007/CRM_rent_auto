<script setup>
import {reactive, ref} from "vue";
import AlertModal from "@components/Modals/AlertModal.vue";

const props = defineProps({
  sending: {
    type: Boolean,
    required: false,
    default: false
  },
  value: {
    type: Object,
    required: false
  },
  maxFileSize: {
    type: Number,
    required: false,
    default: 10000000
  }
});
const emit = defineEmits(['onSelect', 'onDelete']);

const state = reactive({
  image: props.value && props.value !== "" ? import.meta.env.VITE_FILE_URL + props.value : null,
  modal: false
});

async function readFileAsDataURL(file) {
  return await new Promise((resolve) => {
    let fileReader = new FileReader();
    fileReader.onload = (e) => resolve(fileReader.result);
    fileReader.readAsDataURL(file);
  });
}

const onFileSelect = async (data) => {
  state.image = await readFileAsDataURL(data.files[0]);
  emit('onSelect', data);
}
const onDelete = () => {
  state.image = null;
  state.modal = false;
  emit('onDelete');
}
</script>

<template>
  <OverlayBadge v-if="state.image" @click="state.modal = true" value="X" severity="danger" class="inline-flex">
    <Image :src="state.image" size="xlarge" width="250"/>
  </OverlayBadge>
  <FileUpload v-if="!state.image" @select="onFileSelect($event)"
              customUpload :show-upload-button="false" :show-cancel-button="false"
              :multiple="false" accept="image/*" :maxFileSize="maxFileSize">
    <template #empty>
      <span>Перетащите файл для загрузки. Максимальный размер фото {{maxFileSize / 1000000}} Мб.</span>
    </template>
  </FileUpload>
  <AlertModal
      target="#modal2"
      :isOpen="state.modal"
      @close="state.modal = false"
      @accept="onDelete"
      title="Вы действительно хотите удалить?"
      withButtons info/>
</template>

<style scoped>

</style>