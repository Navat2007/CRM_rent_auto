<script setup>
import moment from "moment/moment.js";

const props = defineProps({
  item: {
    type: Object,
    required: false,
    default: undefined
  },
  day: {
    type: Object,
    required: true
  },
  month: {
    type: Number,
    required: true
  },
  year: {
    type: Number,
    required: true
  },
});
const emit = defineEmits(['onSelect']);

const getBackgroundColor = () => {
  //const currentDate = moment(`${props.year}-${props.month}-${props.day.title}`, 'YYYY-MM-DD');
  const currentDate = moment();

  if(currentDate.isBefore(moment(props.item.start_date, 'YYYY-MM-DD HH:mm:ss'))){
    return 'bg-yellow-500'
  }
  else if (currentDate.isAfter(moment(props.item.end_date, 'YYYY-MM-DD HH:mm:ss'))){
    return 'bg-blue-500'
  }
  else if(currentDate.isBetween(
      moment(props.item.start_date, 'YYYY-MM-DD HH:mm:ss'),
      moment(props.item.end_date, 'YYYY-MM-DD HH:mm:ss'),
      undefined, '[]'
  )){
    return 'bg-green-500'
  }
}

</script>

<template>
  <div
      v-if="item"
      class="flex justify-center px-4 hover:bg-gray-200 dark:hover:bg-gray-600"
      :class="{
        [getBackgroundColor()]: true
      }"
      @click="() => emit('onSelect', item)"
  >
    <span>{{ item.id }}</span>
  </div>
  <div
      v-else
      class="flex justify-center px-4 hover:bg-gray-200 dark:hover:bg-gray-600"
      :class="{
        'bg-blue-200': day.weekend,
      }"
  >
    &nbsp;
  </div>
</template>

<style scoped>

</style>