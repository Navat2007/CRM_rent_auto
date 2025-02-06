<script setup>
import {onMounted, ref} from "vue";
import router from "@router";
import {useRoute} from 'vue-router';

import DirectoryService from "@services/DirectoryService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import DirectoryEditForm from "@components/Forms/Directory/DirectoryEditForm.vue";
import DirectoryPricePeriodsEditForm from "@components/Forms/Directory/PricePeriods/DirectoryPricePeriodsEditForm.vue";

const item = ref(null);
const route = useRoute();
const error = ref('');
const loading = ref(true);
const sending = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);
const isDeleteModalOpen = ref(false);

const directoryParentTitle = 'Ценовые периоды';
const directoryTitle = 'Редактирование ценового периода';
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

const handleEdit = (data) => {
    data.id = route.params.id;
    data.name = `от ${data.days_from}` + (data.days_until ? ` до ${data.days_until}` : '');

    DirectoryService.editPricePeriod(data).then((response) => {
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
const handleDelete = () => {
    let sendingData = {
        id: route.params.id,
    };
    isDeleteModalOpen.value = false;
    sending.value = true;

    DirectoryService.deletePricePeriod(sendingData).then((response) => {
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

async function fetchData() {
    item.value = await DirectoryService.getPricePeriodById({id: route.params.id});
    loading.value = false;
}

onMounted(fetchData);
</script>

<template>
    <PageContainer :loading="loading" :breadcrumbs="breadcrumbs">
        <DirectoryPricePeriodsEditForm :title="directoryTitle" @onSubmit="handleEdit"
                                       @onDelete="isDeleteModalOpen = true" :item="item"/>

        <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
        <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
        <AlertModal
            target="#modal2"
            :isOpen="isDeleteModalOpen"
            @close="isDeleteModalOpen = false"
            @accept="handleDelete"
            title="Вы действительно хотите удалить?"
            withButtons info/>
    </PageContainer>
</template>

<style scoped>

</style>