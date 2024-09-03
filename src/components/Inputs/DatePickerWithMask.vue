<script setup>
import {reactive, ref} from "vue";
import moment from "moment";
import BaseModal from "@components/Modals/BaseModal.vue";

const props = defineProps({
  value: {
    type: String,
    required: false
  }
});
const emit = defineEmits(['onChange']);

const date = ref(props.value ? moment(props.value, 'DD.MM.YYYY').toDate() : moment().toDate());
const state = reactive({
  date: props.value || null,
  showHireDateCalendar: false,
  isComplete: false
});

const toggleModal = () => {
  state.showHireDateCalendar = !state.showHireDateCalendar;
}
const handleMaskComplete = () => {
  state.isComplete = true;
  emit('onChange', state.date);
}
const handleDateSelect = (date) => {
  const formattedDate = moment(date).format("DD.MM.YYYY");
  state.date = formattedDate;
  state.showHireDateCalendar = false;
  emit('onChange', formattedDate);
}
const handleClear = () => {
  state.date = null;
  state.showHireDateCalendar = false;
  emit('onChange', state.date);
}
const handleBlur = () => {
  if(state.date.length === 0)
  {
    handleClear();
  }
}
</script>

<template>
  <InputGroup class="w-full">
    <InputMask v-model="state.date" :modelValue="state.date"
               @complete="handleMaskComplete" @blur="handleBlur"
               mask="99.99.9999" placeholder="дд.мм.гггг" fluid/>
    <InputGroupAddon v-if="state.date" @click="handleClear" class="input-group-addon">
      <i class="pi pi-times"></i>
    </InputGroupAddon>
    <InputGroupAddon @click="toggleModal" class="input-group-addon">
      <i class="pi pi-calendar"></i>
    </InputGroupAddon>
  </InputGroup>
  <BaseModal :is-open="state.showHireDateCalendar" @close="toggleModal" title="Выберите дату">
    <DatePicker v-model="date" @date-select="handleDateSelect" inline show-button-bar fluid/>
  </BaseModal>
</template>

<style scoped>
.input-group-addon {
  @apply cursor-pointer hover:text-gray-900 dark:hover:text-gray-200 transition-all duration-200;
}
</style>