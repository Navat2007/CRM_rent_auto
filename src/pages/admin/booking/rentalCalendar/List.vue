<script setup>
import {computed, ref, watchEffect} from "vue";
import moment from "moment";

import {useAuthStore} from "@stores";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import BookingService from "@services/BookingService.js";
import BookingCalendarCell from "@components/Table/Cells/BookingCalendarCell.vue";

const {user} = useAuthStore();

const items = ref([]);
const loading = ref(false);
const selectedYear = ref(new Date());
const selectedMonth = ref(new Date());
const days = computed(() => {
  const year = moment(selectedYear.value).year();
  const month = moment(selectedMonth.value).month() + 1;
  const startDate = moment(year + '-' + month + '-01');
  const endDate = moment(year + '-' + month + '-' + startDate.daysInMonth());

  let array = [];

  for (let m = moment(startDate); m.diff(endDate, 'days') <= 0; m.add(1, 'days')) {
    array.push({
      title: m.format('DD'),
      weekend: m.day() === 0 || m.day() === 6,
    })
  }

  return array;
});
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

const handleCellClick = (item) => {
  selectedContract.value = item;
  drawer.value = true;
}
const handleOpenContract = () => {
  window.open('/Admin/booking/rentalContracts/' + selectedContract.value.id, '_blank');
}

const getContractComponent = (data, day) => {
  if (data?.contracts?.length > 0) {
    const year = moment(selectedYear.value).year();
    const month = moment(selectedMonth.value).month() + 1;
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

watchEffect(() => {
  fetchData();
}, [days])

async function fetchData() {
  loading.value = true;

  const year = moment(selectedYear.value).year();
  const month = moment(selectedMonth.value).month() + 1;
  const startDate = moment(year + '-' + month + '-01');

  items.value = await BookingService.getCalendar({
    company_id: user.company_id,
    year: year,
    month: month,
    day_in_month: startDate.daysInMonth(),
  });

  loading.value = false;
}
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <div class="flex flex-col sm:flex-row gap-2 mb-4">
      <FloatLabel variant="on">
        <DatePicker
            v-model="selectedYear" view="year" dateFormat="yy"
            :disabled="loading"
            @valueChange="(e)=> {
              selectedMonth = new Date(e.getFullYear(), selectedMonth.getMonth(), 1);
            }"
        />
        <label for="on_label">Год</label>
      </FloatLabel>
      <FloatLabel variant="on">
        <DatePicker
            v-model="selectedMonth" view="month" dateFormat="MM"
            :disabled="loading"
            @valueChange="(e)=> {
              selectedYear = e;
            }"
        />
        <label for="on_label">Месяц</label>
      </FloatLabel>
    </div>
    <DataTable :value="items" tableStyle="min-width: 50rem" :loading="loading">
      <Column field="id" header="ID авто">
      </Column>
      <Column field="state_number" header="Гос номер">
      </Column>
      <Column header="Авто">
        <template #body="slotProps">
          <div class="flex flex-row gap-1">
            <span>{{ slotProps.data.brand }}</span>
            <span>{{ slotProps.data.model }}</span>
          </div>
        </template>
      </Column>

      <Column
          v-for="day in days"
          :pt="{
            bodyCell: 'p-0 border border-gray-100 dark:border-gray-800 hover:bg-gray-100 dark:hover:bg-gray-800'
          }"
      >
        <template #header="slotProps">
          {{ day.title + (day.weekend ? '*' : '') }}
        </template>
        <template #body="{data}">
          <BookingCalendarCell
              :item="getContractComponent(data, day)"
              :day="day"
              :month="moment(selectedMonth.value).month() + 1"
              :year="moment(selectedYear.value).year()"
              @onSelect="handleCellClick"
          />
        </template>
      </Column>

      <!--      <Column field="summary" header="Сумма">-->
      <!--      </Column>-->
      <!--      <Column field="tariff" header="Тариф">-->
      <!--      </Column>-->
      <!--      <Column field="balance" header="Баланс">-->
      <!--      </Column>-->
      <!--      <Column field="object" header="Объект">-->
      <!--      </Column>-->
      <!--      <Column field="client" header="Клиент">-->
      <!--      </Column>-->
    </DataTable>
  </PageContainer>

  <Drawer
      position="right" class="w-full md:w-3/5 lg:w-2/5 xl:w-2/6"
      v-model:visible="drawer"
      header="Договор проката"
  >
    <div class="flex flex-col mb-8">
      <div class="mb-4">
        <img class="w-full h-64 object-cover" v-if="selectedContract.avatar" alt="car photo" :src="selectedContract.avatar"/>
        <img class="w-full h-64 object-cover" v-else src="@/assets/images/client-placeholder.png" alt="car photo"/>
      </div>

      <div class="flex justify-between gap-2"><span>Договор:</span><span
          class="font-bold">{{ selectedContract.id }} от {{ moment(selectedContract.start_date).format('DD.MM.YYYY HH:mm') }}</span></div>
      <div class="flex justify-between gap-2"><span>Возврат:</span><span
          class="font-bold">{{ moment(selectedContract.end_date).format('DD.MM.YYYY HH:mm') }}</span></div>
      <div class="flex justify-between gap-2"><span>ФИО:</span><span
          class="font-bold">{{ selectedContract.fio === null || selectedContract.fio === '' ? 'Не указан' : selectedContract.fio }}</span></div>
      <div class="flex justify-between gap-2"><span>Email:</span><span
          class="font-bold">{{ selectedContract.email === null || selectedContract.email === '' ? 'Не указан' : selectedContract.email }}</span></div>
      <div class="flex justify-between gap-2"><span>Телефон:</span><span
          class="font-bold">{{ selectedContract.phone === null || selectedContract.phone === '' ? 'Не указан' : selectedContract.phone }}</span></div>
    </div>

    <Button icon="pi pi-sign-in" aria-label="Save" label="Открыть договор" fluid @click="handleOpenContract"/>
  </Drawer>
</template>