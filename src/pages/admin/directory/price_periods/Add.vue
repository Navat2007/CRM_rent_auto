<script setup>
import {ref} from "vue";
import router from "@router";

import DirectoryService from "@services/DirectoryService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import DirectoryAddForm from "@components/Forms/Directory/DirectoryAddForm.vue";
import DirectoryPricePeriodsAddForm from "@components/Forms/Directory/PricePeriods/DirectoryPricePeriodsAddForm.vue";

const error = ref('');
const loading = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const directoryParentTitle = 'Ценовые периоды';
const directoryTitle = 'Добавление ценового периода';
const directoryUrl = 'price_periods';
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
    data.name = `от ${data.days_from}` + (data.days_until ? ` до ${data.days_until}` : '');

    DirectoryService.addPricePeriod(data).then((response) => {
        //console.log(response.data);
        if (response.data) {
            if (parseInt(response.data.error) === 0) {
                isSuccessModalOpen.value = true
            } else {
                error.value = response.data.error_text
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
        <DirectoryPricePeriodsAddForm :title="directoryTitle" @onSubmit="handleAdd"/>

        <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
        <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
    </PageContainer>
</template>

<style scoped>

</style>