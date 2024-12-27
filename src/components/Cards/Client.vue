<script setup>
import moment from "moment";

const props = defineProps({
  item: {
    type: Object,
    required: false,
    default: null
  },
  avatar: {
    type: String,
    required: false,
    default: ''
  },
  fio: {
    type: String,
    required: false,
    default: 'ФИО не указано'
  },
  phone: {
    type: String,
    required: false,
    default: null
  },
  email: {
    type: String,
    required: false,
    default: null
  },
  birth_date: {
    type: String,
    required: false,
    default: null
  },
  gender: {
    type: String,
    required: false,
    default: null
  },
});

const openWindow = () => {
  window.open('/Admin/clients/' + props.item.id, '_blank');
}
</script>

<template>
  <Card
      v-if="item" style="overflow: hidden"
      class="shadow-card hover:shadow-cardHover transition-shadow active:shadow-card"
      pt:root="sm:flex-row" pt:header="sm:flex-auto sm:w-1/2"
      pt:body="sm:flex-auto sm:w-1/2" pt:content="sm:mb-auto">
    <template #header>
      <div >
        <img class="w-full h-full object-cover" v-if="item.avatar" alt="car photo" :src="item.avatar" />
        <img class="w-full h-full object-cover" v-else src="@/assets/images/client-placeholder.png" alt="car photo" />
      </div>
    </template>
    <template #title>
      <span class="text-2xl">{{item.full_name}}</span>
    </template>
    <template #content>
      <div class="grid gap-2 grid-cols-1 mt-2">
        <div class="flex justify-between"><span>Email</span><span class="font-bold">{{item.email === null || item.email === '' ? 'Не указан' : item.email}}</span></div>
        <div class="flex justify-between"><span>Телефон</span><span class="font-bold">{{item.phone === null || item.phone === '' ? 'Не указан' : item.phone}}</span></div>
        <div class="flex justify-between">
          <span>Дата рождения</span>
          <span class="font-bold">{{item.birth_date === null ? 'Не указана' : moment(item.birth_date).format('DD.MM.YYYY')}}</span>
        </div>
        <div class="flex justify-between">
          <span>Пол</span>
          <span class="font-bold">{{item.gender === null ? 'Не указана' : item.gender}}</span>
        </div>
      </div>
    </template>
    <template #footer>
      <div class="flex justify-end gap-4 mt-1">
        <Button @click="openWindow" label="Открыть" severity="primary" size="large"/>
      </div>
    </template>
  </Card>
  <Card
      v-else style="overflow: hidden"
      class="shadow-card hover:shadow-cardHover transition-shadow active:shadow-card"
      pt:root="sm:flex-row" pt:header="sm:flex-auto sm:w-1/2"
      pt:body="sm:flex-auto sm:w-1/2" pt:content="sm:mb-auto">
    <template #header>
      <div>
        <img class="w-full h-full object-cover" v-if="avatar" alt="car photo" :src="avatar" />
        <img class="w-full h-full object-cover" v-else src="@/assets/images/client-placeholder.png" alt="car photo" />
      </div>
    </template>
    <template #title>
      <span class="text-2xl">{{fio}}</span>
    </template>
    <template #content>
      <div class="grid gap-2 grid-cols-1 mt-2">
        <div class="flex justify-between"><span>Email</span><span class="font-bold">{{email === null || email === '' ? 'Не указан' : email}}</span></div>
        <div class="flex justify-between"><span>Телефон</span><span class="font-bold">{{phone === null || phone === '' ? 'Не указан' : phone}}</span></div>
        <div class="flex justify-between">
          <span>Дата рождения</span>
          <span class="font-bold">{{birth_date === null ? 'Не указана' : moment(birth_date).format('DD.MM.YYYY')}}</span>
        </div>
        <div class="flex justify-between">
          <span>Пол</span>
          <span class="font-bold">{{gender === null ? 'Не указана' : gender}}</span>
        </div>
      </div>
    </template>
    <template #footer>
      <div class="flex justify-end gap-4 mt-1">
        <Button @click="openWindow" label="Открыть" severity="primary" size="large"/>
      </div>
    </template>
  </Card>
</template>

<style scoped>

</style>