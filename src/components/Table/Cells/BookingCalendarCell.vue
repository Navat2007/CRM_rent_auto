<script setup>
import {onMounted, ref} from "vue";
import {useMobileDetection} from "vue3-mobile-detection";
import moment from "moment";
import Popover from 'primevue/popover';
import {PlusRound} from "@vicons/material";
import {Icon} from "@vicons/utils";

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
});
const popover = ref();
const drawer = ref(false);
const {isMobile} = useMobileDetection();

const openWindow = () => {
  window.open('/Admin/booking/rentalContracts/' + props.item.id, '_blank');
}

onMounted(() => {
  console.log(props.item);
})
</script>

<template>
  <div v-if="item" class="flex justify-center ">
    <div v-if="isMobile()" @click="(e) => drawer = true">
      {{ item.id }}
    </div>
    <div v-else @click="(e) => drawer = true">
      {{ item.id }}
    </div>
  </div>

  <Popover ref="popover" pt:content="p-2">
    <div class="flex justify-between mb-4">
      <div class="flex flex-col">
        <div class="flex gap-2"><span>Договор:</span><span
            class="font-bold">{{ item.id }} от {{ moment(item.start_date).format('DD.MM.YYYY HH:mm') }}</span></div>
        <div class="flex gap-2"><span>Возврат:</span><span
            class="font-bold">{{ moment(item.end_date).format('DD.MM.YYYY HH:mm') }}</span></div>
      </div>
      <Button icon="pi pi-sign-in" aria-label="Save" @click="openWindow"/>
    </div>

    <div class="flex flex-row">
      <div>
        <img class="w-32 h-24 object-cover" v-if="item.avatar" alt="car photo" :src="item.avatar"/>
        <img class="w-32 h-24 object-cover" v-else src="@/assets/images/client-placeholder.png" alt="car photo"/>
      </div>
      <div class="flex flex-col pl-2 pr-2">
        <div class="flex justify-between gap-2"><span>ФИО:</span><span
            class="font-bold">{{ item.fio === null || item.fio === '' ? 'Не указан' : item.fio }}</span></div>
        <div class="flex justify-between gap-2"><span>Email:</span><span
            class="font-bold">{{ item.email === null || item.email === '' ? 'Не указан' : item.email }}</span></div>
        <div class="flex justify-between gap-2"><span>Телефон:</span><span
            class="font-bold">{{ item.phone === null || item.phone === '' ? 'Не указан' : item.phone }}</span></div>
      </div>
    </div>
  </Popover>
  <Drawer
      position="right" class="w-full md:w-3/5 lg:w-2/5 xl:w-2/6"
      v-model:visible="drawer"
      header="Договор проката"
  >
    <div class="flex flex-col mb-8">
      <div class="mb-4">
        <img class="w-full h-64 object-cover" v-if="item.avatar" alt="car photo" :src="item.avatar"/>
        <img class="w-full h-64 object-cover" v-else src="@/assets/images/client-placeholder.png" alt="car photo"/>
      </div>

      <div class="flex justify-between gap-2"><span>Договор:</span><span
          class="font-bold">{{ item.id }} от {{ moment(item.start_date).format('DD.MM.YYYY HH:mm') }}</span></div>
      <div class="flex justify-between gap-2"><span>Возврат:</span><span
          class="font-bold">{{ moment(item.end_date).format('DD.MM.YYYY HH:mm') }}</span></div>
      <div class="flex justify-between gap-2"><span>ФИО:</span><span
          class="font-bold">{{ item.fio === null || item.fio === '' ? 'Не указан' : item.fio }}</span></div>
      <div class="flex justify-between gap-2"><span>Email:</span><span
          class="font-bold">{{ item.email === null || item.email === '' ? 'Не указан' : item.email }}</span></div>
      <div class="flex justify-between gap-2"><span>Телефон:</span><span
          class="font-bold">{{ item.phone === null || item.phone === '' ? 'Не указан' : item.phone }}</span></div>
    </div>

    <Button icon="pi pi-sign-in" aria-label="Save" label="Открыть договор" fluid @click="openWindow"/>
  </Drawer>
</template>

<style scoped>

</style>