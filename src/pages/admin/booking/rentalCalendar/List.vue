<script setup>
import {computed, onMounted, ref} from "vue";
import router from "@router";
import moment from "moment";

import {useAuthStore} from "@stores";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";

const {user} = useAuthStore();

const items = ref([]);
const loading = ref(true);
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

async function fetchData() {
  loading.value = false;
}

onMounted(() => {
  fetchData();
});
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <div class="flex flex-row gap-2 mb-4">
      <FloatLabel variant="on">
        <DatePicker
            v-model="selectedYear" view="year" dateFormat="yy"
            @valueChange="(e)=> {
              selectedMonth = new Date(e.getFullYear(), selectedMonth.getMonth(), 1);
            }"
        />
        <label for="on_label">Год</label>
      </FloatLabel>
      <FloatLabel variant="on">
        <DatePicker
            v-model="selectedMonth" view="month" dateFormat="MM"
            @valueChange="(e)=> {
              selectedYear = e;
            }"
        />
        <label for="on_label">Месяц</label>
      </FloatLabel>
    </div>
    <DataTable :value="items" tableStyle="min-width: 50rem" rowHover>
      <Column field="code" header="Код">
      </Column>
      <Column field="model" header="Модель">
      </Column>

      <Column v-for="day in days">
        <template #header="slotProps">
          {{ day.title + (day.weekend ? '*' : '')}}
        </template>
        <template #body="slotProps">

        </template>
      </Column>

      <Column field="summary" header="Сумма">
      </Column>
      <Column field="tariff" header="Тариф">
      </Column>
      <Column field="balance" header="Баланс">
      </Column>
      <Column field="object" header="Объект">
      </Column>
      <Column field="client" header="Клиент">
      </Column>
    </DataTable>
  </PageContainer>
</template>