<script setup lang="ts">
import {Icon} from "@vicons/utils";

enum Layouts {
  Full = "Full",
  Small = "Small",
  OneString = "OneString",
}

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  layout: {
    type: String as () => Layouts,
    default: Layouts.Full
  }
});

const openWindow = () => {
  window.open('/Admin/auto/' + props.item.id, '_blank');
}
</script>

<template>
  <Card
      v-if="layout === Layouts.Full" style="overflow: hidden"
      class="shadow-card hover:shadow-cardHover transition-shadow active:shadow-card"
      pt:root="sm:flex-row" pt:header="sm:flex-auto sm:w-1/2" pt:body="sm:flex-auto sm:w-1/2"
  >
    <template #header>
      <img v-if="item.avatar" class="w-full h-full object-cover" alt="car photo" :src="item.avatar"/>
      <img v-else class="w-full h-full object-cover" src="@/assets/images/car-placeholder2.png" alt="car photo"/>
    </template>
    <template #title>
      <div class="flex justify-between">
        <span class="flex items-center text-2xl">{{ item.brand }} {{ item.model }}</span>
        <span
            class="flex items-center text-sm text-gray-800 border-2 border-dashed rounded-xl p-1">{{ item.state_number }}</span>
      </div>

    </template>
    <template #subtitle><span class="flex items-center justify-center"></span></template>
    <template #content>
      <div class="grid gap-2 grid-cols-1 mt-2">
        <div class="flex justify-between"><span>Класс</span><span class="font-bold">{{ item.class }}</span></div>
        <div class="flex justify-between"><span>Комплектация</span><span
            class="font-bold">{{ item.configuration }}</span></div>
        <div class="flex justify-between"><span>Поколение</span><span class="font-bold">{{ item.generation }}</span>
        </div>
        <div class="flex justify-between"><span>Тип топлива</span><span class="font-bold">{{ item.fuel_type }}</span>
        </div>
      </div>
    </template>
    <template #footer>
      <div class="flex justify-end gap-4 mt-1">
        <Button @click="openWindow" label="Открыть" severity="primary" size="large"/>
      </div>
    </template>
  </Card>
  <Card v-if="layout === Layouts.Small" style="overflow: hidden" class="shadow-card hover:shadow-cardHover transition-shadow active:shadow-card">
    <template #title>
      <span class="text-sm text-gray-800 border-2 border-dashed rounded-xl p-1">{{ item.state_number }}</span>
      <div class="flex justify-between mt-2">
        <span class="flex items-center text-2xl">{{ item.brand }} {{ item.model }}</span>
        <Icon @click="openWindow">

        </Icon>
      </div>

    </template>
    <template #subtitle><span class="flex items-center justify-center"></span></template>
    <template #content>
      <img v-if="item.avatar" alt="car photo" :src="item.avatar"/>
      <div v-else>
        <img src="@/assets/images/car-placeholder.png" alt="car photo"/>
      </div>
      <div class="grid gap-2 grid-cols-1 mt-2">
        <div class="flex justify-between"><span>Класс</span><span class="font-bold">{{ item.class }}</span></div>
        <div class="flex justify-between"><span>Комплектация</span><span
            class="font-bold">{{ item.configuration }}</span></div>
        <div class="flex justify-between"><span>Поколение</span><span class="font-bold">{{ item.generation }}</span>
        </div>
        <div class="flex justify-between"><span>Тип топлива</span><span class="font-bold">{{ item.fuel_type }}</span>
        </div>
      </div>
    </template>
  </Card>
  <Card v-if="layout === Layouts.OneString" class="shadow-card hover:shadow-cardHover transition-shadow active:shadow-card">
    <template #content>
      <div class="flex flex-wrap gap-2">
        <span class="font-bold">{{ item.brand }} {{ item.model }}</span>
        <span v-if="item.state_number">{{ item.state_number }}.</span>
        <span v-if="item.class">Класс:</span><span v-if="item.class" class="font-bold">{{ item.class }}</span>
        <span v-if="item.configuration">Комплектация:</span><span v-if="item.configuration" class="font-bold">{{ item.configuration }}</span>
        <span v-if="item.generation">Поколение:</span><span v-if="item.generation" class="font-bold">{{ item.generation }}</span>
        <span v-if="item.fuel_type">Тип топлива:</span><span v-if="item.fuel_type" class="font-bold">{{ item.fuel_type }}</span>
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