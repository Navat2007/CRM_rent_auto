<script setup lang="ts">
import {useSidebarStore} from '@stores/sidebar.js'
import {ref} from 'vue'
import {useRoute} from "vue-router";

const sidebarStore = useSidebarStore()

const props = defineProps(['items', 'page'])
const items = ref(props.items)
const currentPage = useRoute().name
const opened = ref(false);

const handleItemClick = (index: number) => {
  sidebarStore.selected = sidebarStore.selected === props.items[index].label ? '' : props.items[index].label
}
</script>

<template>
  <ul class="mt-4 mb-5.5 flex flex-col gap-2.5 pl-8">
    <template v-for="(childItem, index) in items" :key="index">
      <li v-if="childItem.visible">
        <router-link
          :to="childItem.route"
          @click="handleItemClick(index)"
          class="group relative flex items-center gap-2.5 rounded-md px-4 font-medium text-bodydark2 duration-300 ease-in-out hover:text-white"
          :class="{
            '!text-white': childItem.label === sidebarStore.selected
          }"
        >
          {{ childItem.label }}
        </router-link>
      </li>
    </template>
  </ul>
</template>
