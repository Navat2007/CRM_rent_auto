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

  if(currentDate.isBefore(moment(props.item.start_date, 'YYYY-MM-DD'))){
    return 'bg-yellow-500'
  }
  else if (currentDate.isAfter(moment(props.item.end_date, 'YYYY-MM-DD'))){
    return 'bg-blue-500'
  }
  else if(currentDate.isBetween(
      moment(props.item.start_date, 'YYYY-MM-DD'),
      moment(props.item.end_date, 'YYYY-MM-DD'),
      undefined, '[]'
  )){
    return 'bg-green-500'
  }
}

</script>

<template>
  <div
      v-if="item"
      class="flex justify-center py-1.5 px-4 hover:bg-gray-100 dark:hover:bg-gray-800"
      :class="{
        [getBackgroundColor()]: true
      }"
      @click="() => emit('onSelect', item)"
  >
    <span>{{ item.id }}</span>
  </div>
  <div
      v-else
      class="flex justify-center py-1.5 px-4 hover:bg-gray-100 dark:hover:bg-gray-800"
      :class="{
        'bg-blue-200': day.weekend,
      }"
  >
    &nbsp;
  </div>
</template>

<style scoped>

</style>