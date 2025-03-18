<script setup>
import {ref, watch, onMounted, computed, nextTick} from "vue";
import moment from "moment";

import {useAuthStore} from "@stores";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import BookingService from "@services/BookingService.js";
import BookingCalendarCell from "@components/Table/Cells/BookingCalendarCell.vue";

const {user} = useAuthStore();

const items = ref([]);
const filteredItems = ref([]);
const loading = ref(false);
const calculate = ref(false);
const date = ref(new Date());
const days = ref([]);
const selectedContract = ref(null);
const drawer = ref(false);

const breadcrumbs = ref([
    {
        name: 'Бронирование',
        route: null
    },
    {
        name: 'Календарь занятости авто',
        route: null
    },
]);

const filterFree = ref(false);
const filterBooking = ref(false);
const filterUnfree = ref(false);

const currentHoverDay = ref(null);
const currentHoverCar = ref(null);

const handleCellClick = (item) => {
    selectedContract.value = item;
    drawer.value = true;
}
const handleStateNumberCellClick = (item) => {
    window.open('/Admin/auto/' + item.id, '_blank');
}
const handleOpenContract = () => {
    window.open('/Admin/booking/rentalContracts/' + selectedContract.value.id, '_blank');
}

const getContractComponent = (data, day) => {
    if (data?.contracts?.length > 0) {
        const year = moment(date.value).year();
        const month = moment(date.value).month() + 1;
        const currentDate = moment(`${year}-${month}-${day.title}`, 'YYYY-MM-DD');

        return data.contracts.find(
            contract => currentDate.isBetween(
                moment(contract.start_date, 'YYYY-MM-DD'),
                moment(contract.end_date, 'YYYY-MM-DD'),
                undefined, '[]'
            ));
    }

    return undefined;
}

watch(() => date.value, () => {
    setTimeout(async () => {
        await fetchData();
    }, 250);
});

watch([filterFree, filterBooking, filterUnfree], () => {
    calculate.value = true;

    setTimeout(async () => {
        await calculateFilters();
    }, 250);
});

async function fetchData() {
    loading.value = true;

    const year = moment(date.value).year();
    const month = moment(date.value).month() + 1;
    const startDate = moment(year + '-' + month + '-01');

    await BookingService.getCalendar({
        company_id: user.company_id,
        year: year,
        month: month,
        day_in_month: startDate.daysInMonth(),
    }).then((result) => {
        items.value = result;
        filteredItems.value = result;

        const endDate = moment(year + '-' + month + '-' + startDate.daysInMonth());

        let array = [];

        for (let m = moment(startDate); m.diff(endDate, 'days') <= 0; m.add(1, 'days')) {
            array.push({
                title: m.format('DD'),
                weekend: m.day() === 0 || m.day() === 6,
            })
        }

        days.value = array;
    }).finally(() => {
        loading.value = false;
        calculateFilters();
    });
}

async function calculateFilters(){
    const addItem = (data) => {
        if(result.filter(item => parseInt(item.id) === parseInt(data.id)).length === 0){
            result.push(data);
        }
    }

    let result = [];
    const currentDate = moment();

    for(const item of items.value){
        if(!filterFree.value && !filterBooking.value && !filterUnfree.value) {
            addItem(item);
        }
        else {
            if(filterFree.value) {
                if(item.contracts.length === 0)
                    addItem(item);
                else{
                    let isFree = true;

                    for(const contract of item.contracts) {
                        if (currentDate.isBefore(moment(contract.end_date, 'YYYY-MM-DD HH:mm:ss')))
                        {
                            isFree = false;
                            break;
                        }
                    }

                    if(isFree)
                        addItem(item);
                }
            }

            if(filterBooking.value) {
                if(item.contracts.length > 0)
                {
                    for(const contract of item.contracts) {
                        if(currentDate.isBefore(moment(contract.start_date, 'YYYY-MM-DD HH:mm:ss')))
                        {
                            addItem(item);
                            break;
                        }
                    }
                }
            }

            if(filterUnfree.value) {
                if(item.contracts.length > 0)
                {
                    for(const contract of item.contracts) {
                        if(currentDate.isBetween(
                            moment(contract.start_date, 'YYYY-MM-DD HH:mm:ss'),
                            moment(contract.end_date, 'YYYY-MM-DD HH:mm:ss'),
                            undefined, '[]'
                        ))
                        {
                            console.log(contract);
                            addItem(item);
                            break;
                        }
                    }
                }
            }
        }
    }

    calculate.value = false;

    filteredItems.value = result;
}

onMounted(fetchData);
</script>

<template>
    <PageContainer :breadcrumbs="breadcrumbs">
        <div class="flex flex-col sm:flex-row gap-2 mb-4">
            <FloatLabel variant="on">
                <DatePicker
                    v-model="date" view="year" dateFormat="yy"
                    :disabled="loading || calculate"
                />
                <label for="on_label">Год</label>
            </FloatLabel>
            <FloatLabel variant="on">
                <DatePicker
                    v-model="date" view="month" dateFormat="MM"
                    :disabled="loading || calculate"
                />
                <label for="on_label">Месяц</label>
            </FloatLabel>
            <Button label="Свободные" :disabled="loading || calculate" :variant="filterFree ? '' : 'outlined'" @click="filterFree = !filterFree"/>
            <Button label="Забронированные" :disabled="loading || calculate" :variant="filterBooking ? '' : 'outlined'"
                    @click="filterBooking = !filterBooking"/>
            <Button label="Занятые" :disabled="loading || calculate" :variant="filterUnfree ? '' : 'outlined'" @click="filterUnfree = !filterUnfree"/>
        </div>
        <DataTable
            :value="filteredItems" size="small" tableStyle="min-width: 50rem"
            :loading="loading || calculate"
            scrollable scrollHeight="730px"
        >
            <template #empty>
                По выбранным фильтрам не найдено автомобилей
            </template>
            <Column field="id" header="ID авто" frozen
                    :pt="{
                    bodyCell: 'p-0',
                }"
            >
                <template #body="slotProps">
                    <div
                        :class="currentHoverCar && parseInt(currentHoverCar.id) === parseInt(slotProps.data.id) ? 'bg-gray-200 dark:hover:bg-gray-600' : ''"
                        class="px-2"
                    >
                        {{slotProps.data.id}}
                    </div>
                </template>
            </Column>
            <Column field="state_number" style="min-width: 200px" header="Гос номер" frozen
                    :pt="{
                    bodyCell: 'p-0',
                }"
            >
                <template #body="slotProps">
                    <div
                        :class="currentHoverCar && parseInt(currentHoverCar.id) === parseInt(slotProps.data.id) ? 'bg-gray-200 dark:hover:bg-gray-600' : ''"
                        class="px-2 cursor-pointer hover:underline"
                        @click="handleStateNumberCellClick(slotProps.data)"
                    >
                        {{slotProps.data.state_number}}
                    </div>
                </template>
            </Column>
            <Column header="Авто" frozen
                    :pt="{
                    bodyCell: 'p-0',
                }"
            >
                <template #body="slotProps">
                    <div
                        :class="currentHoverCar && parseInt(currentHoverCar.id) === parseInt(slotProps.data.id) ? 'bg-gray-200 dark:hover:bg-gray-600' : ''"
                        class="flex flex-row gap-1 whitespace-nowrap px-2"
                    >
                        <span>{{ slotProps.data.brand }}</span>
                        <span>{{ slotProps.data.model }}</span>
                    </div>
                </template>
            </Column>
            <Column
                v-for="(day,index) in days" ref="day"
                :pt="{
                    bodyCell: 'p-0 border border-gray-100 dark:border-gray-800 hover:bg-gray-100 dark:hover:bg-gray-800',
                    headerCell: 'p-0',
                }"
            >
                <template #header="slotProps">
                    <div
                        :class="currentHoverDay === day ? 'bg-gray-200 dark:hover:bg-gray-600' : ''"
                        class="flex items-center justify-center text-center px-4 h-full"
                    >
                        {{ day.title + (day.weekend ? '*' : '') }}
                    </div>
                </template>
                <template #body="{data}">
                    <div @mouseenter="() => {
                        currentHoverCar = data;
                        currentHoverDay = day;
                    }">
                        <BookingCalendarCell
                            :item="getContractComponent(data, day)"
                            :day="day"
                            :month="moment(date.value).month() + 1"
                            :year="moment(date.value).year()"
                            @onSelect="handleCellClick"
                        />
                    </div>
                </template>
            </Column>
        </DataTable>
    </PageContainer>

    <Drawer
        position="right" class="w-full md:w-3/5 lg:w-2/5 xl:w-2/6"
        v-model:visible="drawer"
        header="Договор проката"
    >
        <div class="flex flex-col mb-8">
            <div class="mb-4">
                <img class="w-full h-64 object-cover" v-if="selectedContract.avatar" alt="car photo"
                     :src="selectedContract.avatar"/>
                <img class="w-full h-64 object-cover" v-else src="@/assets/images/client-placeholder.png"
                     alt="car photo"/>
            </div>

            <div class="flex justify-between gap-2"><span>Договор:</span><span
                class="font-bold">{{
                    selectedContract.id
                }} от {{ moment(selectedContract.start_date).format('DD.MM.YYYY HH:mm') }}</span></div>
            <div class="flex justify-between gap-2"><span>Возврат:</span><span
                class="font-bold">{{ moment(selectedContract.end_date).format('DD.MM.YYYY HH:mm') }}</span></div>
            <div class="flex justify-between gap-2"><span>ФИО:</span><span
                class="font-bold">{{
                    selectedContract.fio === null || selectedContract.fio === '' ? 'Не указан' : selectedContract.fio
                }}</span></div>
            <div class="flex justify-between gap-2"><span>Email:</span><span
                class="font-bold">{{
                    selectedContract.email === null || selectedContract.email === '' ? 'Не указан' : selectedContract.email
                }}</span></div>
            <div class="flex justify-between gap-2"><span>Телефон:</span><span
                class="font-bold">{{
                    selectedContract.phone === null || selectedContract.phone === '' ? 'Не указан' : selectedContract.phone
                }}</span></div>
        </div>

        <Button icon="pi pi-sign-in" aria-label="Save" label="Открыть договор" fluid @click="handleOpenContract"/>
    </Drawer>
</template>