<script setup>
import {ref} from "vue";
import router from "@router";

import DirectoryService from "@services/DirectoryService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import DirectoryCarClassesAddForm from "@components/Forms/Directory/Car/Classes/DirectoryCarClassesAddForm.vue";

const error = ref('');
const loading = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const directoryParentTitle = 'Классы авто';
const directoryTitle = 'Добавление класса авто';
const directoryUrl = 'car_classes';
const breadcrumbs = ref([
    {
        name: directoryParentTitle,
        route: '/Admin/directory/' + directoryUrl
    },
    {
        name: directoryTitle,
        route: null
    }
]);

const handleAdd = (data) => {
    DirectoryService.addCarClass(data).then(async (response) => {
        if (response.data) {
            if (parseInt(response.data.error) === 0) {
                const id = response.data.params.id;

                for await (const item of data.class_services) {
                    await DirectoryService.editCarClassServicePrice({
                        directory_car_classes_id: id,
                        directory_services_id: item.id,
                        price: item.price === '' || !item.price ? 0 : item.price,
                    });
                }

                isSuccessModalOpen.value = true
            } else {
                error.value = response.data.error_text || response.data
                isAlertModalOpen.value = true
            }
        }
    });
}

const onSuccess = () => {
    isSuccessModalOpen.value = false;
    router.push('/Admin/directory/' + directoryUrl);
}
</script>

<template>
    <PageContainer :breadcrumbs="breadcrumbs">
        <DirectoryCarClassesAddForm :title="directoryTitle" @onSubmit="handleAdd"/>

        <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
        <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
    </PageContainer>
</template>

<style scoped>

</style>