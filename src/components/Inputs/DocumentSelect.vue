<script setup>
import {onMounted, reactive, ref} from "vue";
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
const emit = defineEmits(['onSelect', 'onSave', 'onDelete']);

const state = reactive({
    file: props.value && props.value !== "" ? props.value : null,
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
    state.file = data.files[0];
    emit('onSelect', data);
}
const onDelete = () => {
    state.file = null;
    state.modal = false;
    emit('onDelete');
}

onMounted(async () => {
    console.log(props.value);
    console.log(state.file);
});
</script>

<template>
    <div class="flex flex-col gap-4 sm:flex-row items-center justify-between" >
        <span>
            {{state.file?.name}}
        </span>
        <Button v-if="!props.value && state.file" type="submit" icon="pi pi-save" label="Сохранить" :loading="sending" outlined @click="emit('onSave')"/>
        <Button v-if="props.value && props.value !== ''" type="submit" icon="pi pi-trash" label="Удалить" :loading="sending" outlined @click="state.modal = true"/>
    </div>

    <FileUpload v-if="!state.file" @select="onFileSelect($event)"
                customUpload :show-upload-button="false" :show-cancel-button="false"
                :multiple="false" accept=".doc,.docx" :maxFileSize="maxFileSize">
        <template #empty>
            <span>Перетащите файл для загрузки. Максимальный размер файла {{ maxFileSize / 1000000 }} Мб.</span>
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