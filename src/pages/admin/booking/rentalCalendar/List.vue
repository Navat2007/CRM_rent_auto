<script setup>
import {computed, ref, watchEffect} from "vue";
import router from "@router";
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
})

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

const handleRowClick = (item) => {
  router.push('/Admin/booking/rentalContracts/' + item.id);
}

const getBackgroundColor = (data) => {

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

  console.log(items.value);

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
    <DataTable :value="items" tableStyle="min-width: 50rem" rowHover :loading="loading">
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

      <Column v-for="day in days" :pt="{bodyCell: getBackgroundColor(day)}">
        <template #header="slotProps">
          {{ day.title + (day.weekend ? '*' : '') }}
        </template>
        <template #body="{data}">
          <BookingCalendarCell :item="getContractComponent(data, day)"/>
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
</template>