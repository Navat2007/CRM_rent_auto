<script setup>
import Divider from "primevue/divider";
import {useAuthStore} from "@stores";
import {computed, reactive, unref} from "vue";
import {helpers, required} from "@vuelidate/validators";
import {useVuelidate} from "@vuelidate/core";
import FormError from "@components/Inputs/FormError.vue";

const {user} = useAuthStore();

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  sending: {
    type: Boolean,
    required: false,
    default: false
  },
  card: {
    type: Boolean,
    required: false,
    default: true
  },
})
const emit = defineEmits(['onSubmit']);

const state = reactive({
  companyId: user.company_id,
  name: "",
  limit: 0,
  active: true
});
const rules = computed(() => {
  return {
    name: {
      required: helpers.withMessage("Название должно быть заполнено", required),
      $lazy: true
    },
    limit: {
      $lazy: true
    },
  }
});
const v$ = useVuelidate(rules, state);

const onFormSubmit = async (e) => {
  const isFormCorrect = await unref(v$).$validate();

  if (isFormCorrect) {
    emit('onSubmit', state);
  }
};
</script>

<template>
  <Card v-if="card" class="w-full lg:w-2/3">
    <template #title>{{title}}</template>
    <template #content>
      <form @submit.prevent="onFormSubmit">
        <div class="grid gap-4 mb-4 mt-2 sm:grid-cols-1">
          <!-- Название -->
          <div>
            <FloatLabel variant="on">
              <InputText id="name" v-model="state.name" fluid/>
              <label for="name">Название*</label>
            </FloatLabel>
            <FormError :errors="v$.name.$errors"/>
          </div>
          <!-- Лимит пробега в сутки -->
          <div>
            <FloatLabel variant="on">
              <InputNumber id="limit" min="0" v-model="state.limit" fluid/>
              <label for="limit">Лимит пробега в сутки (км)</label>
            </FloatLabel>
            <FormError :errors="v$.limit.$errors"/>
          </div>
          <!-- Активен? -->
          <div class="flex items-center">
            <input id="active" type="checkbox"
                   v-model="state.active"
                   class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
            <label for="active" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
              Активен?
            </label>
          </div>
        </div>
        <Divider type="dashed"/>
        <Button v-if="user.access.directory === 2" type="submit" icon="pi pi-plus" label="Добавить" outlined/>
      </form>
    </template>
  </Card>
  <div v-else>
    <form @submit.prevent="onFormSubmit">
      <div class="grid gap-4 mb-4 mt-2 sm:grid-cols-1">
        <!-- Название -->
        <div>
          <label for="name"
                 class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Название*</label>
          <input
              v-model="state.name"
              type="text" id="name"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
              placeholder="..."
          >
          <FormError :errors="v$.name.$errors"/>
        </div>
        <!-- Лимит пробега в сутки -->
        <div>
          <FloatLabel variant="on">
            <InputNumber id="limit" min="0" v-model="state.limit" fluid/>
            <label for="limit">Лимит пробега в сутки</label>
          </FloatLabel>
          <FormError :errors="v$.limit.$errors"/>
        </div>
        <!-- Активен? -->
        <div class="flex items-center">
          <input id="active" type="checkbox"
                 v-model="state.active"
                 class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
          <label for="active" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
            Активен?
          </label>
        </div>
      </div>
      <Divider type="dashed"/>
      <Button v-if="user.access.directory === 2" type="submit" icon="pi pi-plus" label="Добавить" outlined/>
    </form>
  </div>
</template>