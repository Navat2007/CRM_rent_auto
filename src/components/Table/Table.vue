<script setup>
import {ref, nextTick } from "vue";
import DataTable from "primevue/datatable";
import {FilterMatchMode} from '@primevue/core/api';

const props = defineProps({
    title: {
        type: String,
        required: false
    },
    pageSize: {
        type: Number,
        required: false,
        default: 20
    },
    items: {
        type: Array,
        required: true
    },
    columns: {
        type: Array,
        required: false
    },
    filters: {
        type: Object,
        required: false,
        default: ref({})
    },
    filterFields: {
        type: Array,
        required: false,
        default: ref([])
    },
    loading: {
        type: Boolean,
        required: false,
        default: false
    },
    withFilters: {
        type: Boolean,
        required: false,
        default: true
    },
    inCard: {
        type: Boolean,
        required: false,
        default: true
    },
    filterDisplay: {
        type: String,
        required: false,
        default: 'menu'
    },
})
const emit = defineEmits(['onRowClick']);

const table = ref();
const key = ref(props.title + "_table");
const tableSaveKey = ref(props.title + "_table");
const finalFilters = ref();
const skeletonItems = ref(new Array(10));

const handleRowClick = (item) => {
    emit('onRowClick', item.data);
}
const clearFilter = () => {
    initFilters();
};
const initFilters = async () => {
    finalFilters.value = {
        ...props.filters,
        global: {value: null, matchMode: FilterMatchMode.CONTAINS}
    };

    await nextTick();

    key.value = new Date().getTime().toString();

};
const exportCSV = () => {
    table.value.exportCSV();
};
const onFilterEvent = (event) => {
    let filters = event;

    if(event.filters)
        filters = event.filters;

    if (table.value) {
        for (const column of table.value.columns) {
            for (const key of Object.keys(filters)) {
                if (filters[key].constraints) {
                    for (const filter of filters[key].constraints) {
                        if (column.props.field === key) {
                            if (filter.value) {
                                column.props.pt = {
                                    filterMenuIcon: 'text-primary-600',
                                }
                            } else {
                                column.props.pt = {
                                    filterMenuIcon: '',
                                }
                            }
                        }
                    }
                }
            }
        }
    }


}

initFilters();
</script>

<template>
    <Card v-if="inCard">
        <template v-if="withFilters" #title>
            <div class="mb-2 flex flex-col sm:flex-row gap-2 justify-between">
                <div>
                    <slot name="buttons"/>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <IconField>
                        <InputIcon class="pi pi-search"/>
                        <InputText v-model="finalFilters['global'].value" placeholder="Поиск" fluid/>
                    </IconField>
                    <!--              <Button icon="pi pi-download" label="" class="main-button" @click="exportCSV($event)" />-->
                    <Button type="button" icon="pi pi-filter-slash" outlined label="Очистить"
                            @click="clearFilter()"/>
                </div>
            </div>
            <slot name="checkbox"/>
        </template>

        <template #content>
            <!-- Skeleton -->
            <DataTable v-if="loading" :value="skeletonItems">
                <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header">
                    <template #body>
                        <Skeleton></Skeleton>
                    </template>
                </Column>
            </DataTable>
            <!-- Table -->
            <DataTable
                v-else
                ref="table" :key="key" :value="items" @row-click="handleRowClick"
                stateStorage="local" :stateKey="tableSaveKey" size="small"
                showGridlines stripedRows :paginator="items.length > pageSize" :rows="pageSize"
                :rowsPerPageOptions="[10, 15, 20, 50]"
                columnResizeMode="expand" reorderableColumns
                :sortOrder="1" removableSort rowHover
                :filterDisplay="filterDisplay" v-model:filters="finalFilters" :globalFilterFields="filterFields"
                pt:header="border-0 mr-0 pr-0"
                @filter="onFilterEvent"
            >
                <template #header>
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl">{{ title }}</h2>
                    </div>
                </template>
                <template #empty> Результаты не найдены.</template>
                <template
                    #paginatorcontainer="{ first, last, page, pageCount, prevPageCallback, nextPageCallback, totalRecords }">
                    <div
                        class="flex items-center gap-4 border border-primary bg-transparent rounded-full w-full py-1 px-2 justify-between">
                        <Button icon="pi pi-chevron-left" rounded text @click="prevPageCallback"
                                :disabled="page === 0"/>
                        <div class="text-color font-medium">
                            <span class="hidden sm:block">Показано с {{ first }} по {{ last }} из {{
                                    totalRecords
                                }}</span>
                            <span class="block sm:hidden">Страница {{ page + 1 }} из {{ pageCount }}</span>
                        </div>
                        <Button icon="pi pi-chevron-right" rounded text @click="nextPageCallback"
                                :disabled="page === pageCount - 1"/>
                    </div>
                </template>
                <slot name="columns"/>
            </DataTable>
        </template>
    </Card>
    <div v-else>
        <div v-if="withFilters">
            <div class="mb-2 flex flex-col sm:flex-row gap-2 justify-between">
                <div>
                    <slot name="buttons"/>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <IconField>
                        <InputIcon class="pi pi-search"/>
                        <InputText v-model="finalFilters['global'].value" placeholder="Поиск" fluid/>
                    </IconField>
                    <!--              <Button icon="pi pi-download" label="" class="main-button" @click="exportCSV($event)" />-->
                    <Button type="button" icon="pi pi-filter-slash" outlined label="Очистить"
                            @click="clearFilter()"/>
                </div>
            </div>
            <slot name="checkbox"/>
        </div>
        <div>
            <!-- Skeleton -->
            <DataTable v-if="loading" :value="skeletonItems">
                <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header">
                    <template #body>
                        <Skeleton></Skeleton>
                    </template>
                </Column>
            </DataTable>
            <!-- Table -->
            <DataTable
                v-else
                ref="table" :value="items" @row-click="handleRowClick"
                stateStorage="local" :stateKey="tableSaveKey" size="small"
                showGridlines stripedRows :paginator="items.length > pageSize" :rows="pageSize"
                :rowsPerPageOptions="[10, 20, 50]"
                resizableColumns columnResizeMode="expand" reorderableColumns
                :sortOrder="1" removableSort rowHover
                :filterDisplay="filterDisplay" v-model:filters="finalFilters" :globalFilterFields="filterFields"
                pt:header="border-0 mr-0 pr-0"
                @filter="onFilterEvent"
            >
                <template #header>
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl">{{ title }}</h2>
                    </div>
                </template>
                <template #empty> Результаты не найдены.</template>
                <template
                    #paginatorcontainer="{ first, last, page, pageCount, prevPageCallback, nextPageCallback, totalRecords }">
                    <div
                        class="flex items-center gap-4 border border-primary bg-transparent rounded-full w-full py-1 px-2 justify-between">
                        <Button icon="pi pi-chevron-left" rounded text @click="prevPageCallback"
                                :disabled="page === 0"/>
                        <div class="text-color font-medium">
                            <span class="hidden sm:block">Показано с {{ first }} по {{ last }} из {{
                                    totalRecords
                                }}</span>
                            <span class="block sm:hidden">Страница {{ page + 1 }} из {{ pageCount }}</span>
                        </div>
                        <Button icon="pi pi-chevron-right" rounded text @click="nextPageCallback"
                                :disabled="page === pageCount - 1"/>
                    </div>
                </template>
                <slot name="columns"/>
            </DataTable>
        </div>
    </div>
</template>

<style scoped>
.buttons-container {
    @apply flex flex-col flex-shrink-0 space-y-3 md:flex-row md:items-center justify-end md:space-y-0 md:space-x-3 w-full md:w-1/2;
}

.main-table {

}

.row {
    @apply dark:text-teal-100;
}
</style>