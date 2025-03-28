<script setup lang="ts">
import moment from "moment";

enum Layouts {
  Full = "Full",
  Small = "Small",
  OneString = "OneString",
}

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
  layout: {
    type: String as () => Layouts,
    required: false,
    default: Layouts.Full
  }
});

const openWindow = () => {
  window.open('/Admin/clients/' + props.item.id, '_blank');
}
</script>

<template>
  <Card
      v-if="layout === Layouts.Full" style="overflow: hidden"
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
      v-if="layout === Layouts.OneString" style="overflow: hidden"
      class="shadow-card hover:shadow-cardHover transition-shadow active:shadow-card"
      pt:root="sm:flex-row" pt:header="sm:flex-auto sm:w-1/2"
      pt:body="sm:flex-auto sm:w-1/2" pt:content="sm:mb-auto">
    <template #content>
      <div class="flex flex-wrap gap-2">
        <div v-if="item.full_name" class="flex gap-1"><span>ФИО:</span><span class="font-bold">{{item.full_name}}</span></div>
        <div v-if="item.email" class="flex gap-1"><span>Email:</span><span class="font-bold">{{item.email}}</span></div>
        <div v-if="item.phone" class="flex gap-1"><span>Телефон:</span><span class="font-bold">{{item.phone}}</span></div>
        <div v-if="item.birth_date" class="flex gap-1">
          <span>Дата рождения:</span>
          <span class="font-bold">{{moment(item.birth_date).format('DD.MM.YYYY')}}</span>
        </div>
        <div v-if="item.gender" class="flex gap-1">
          <span>Пол:</span>
          <span class="font-bold">{{item.gender}}</span>
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