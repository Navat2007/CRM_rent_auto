<script setup>
import Divider from "primevue/divider";
import {useAuthStore} from "@stores";
import {computed, onMounted, reactive, ref, unref} from "vue";
import {helpers, required, minValue} from "@vuelidate/validators";
import {useVuelidate} from "@vuelidate/core";
import FormError from "@components/Inputs/FormError.vue";
import DirectoryService from "@services/DirectoryService.js";
import AddModelDrawer from "@components/Drawers/Directory/Car/AddModelDrawer.vue";

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
  model: {
    required: false,
    type: Number,
    default: 0
  }
});
const emit = defineEmits(['onSubmit']);

const loadingModels = ref(true);
const models = ref([]);
const isModelAddDrawerOpen = ref(false);

const state = reactive({
  companyId: user.company_id,
  name: "",
  modelId: props.model,
  active: true
});
const rules = computed(() => {
  return {
    name: {
      required: helpers.withMessage("Название должно быть заполнено", required),
      $lazy: true
    },
    modelId: {
      required: helpers.withMessage("Нужно выбрать модель авто", required),
      minValue: helpers.withMessage("Нужно выбрать модель авто", minValue(1)),
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

const onModelAdd = async (id) => {
  loadingModels.value = true;
  await fetchDirectoryModels();
  state.modelId = id;
}

async function fetchDirectoryModels() {
  models.value = await DirectoryService.getAll({directory: 'directory_car_models', company_id: user.company_id});
  models.value = models.value.filter(item => item.archive === "Активен");
  loadingModels.value = false;
}

onMounted(() => {
  fetchDirectoryModels();
});
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
          <!-- Модель авто -->
          <div>
            <label for="modelI"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Марка авто*</label>
            <Select v-model="state.modelId" :loading="loadingModels" :options="models"
                    optionLabel="name"
                    optionValue="id" placeholder="Выберите модель авто" showClear filter class="w-full">
              <template v-if="user.access.directory === 2" #header>
                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить" outlined
                        @click="isModelAddDrawerOpen = true"/>
              </template>
            </Select>
            <FormError :errors="v$.modelId.$errors"/>
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
          <FloatLabel variant="on">
            <InputText id="name" v-model="state.name" fluid/>
            <label for="name">Название*</label>
          </FloatLabel>
          <FormError :errors="v$.name.$errors"/>
        </div>
        <!-- Модель авто -->
        <div>
          <label for="modelI"
                 class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Марка авто*</label>
          <Select v-model="state.modelId" :loading="loadingModels" :options="models"
                  optionLabel="name"
                  optionValue="id" placeholder="Выберите модель авто" showClear filter class="w-full">
            <template v-if="user.access.directory === 2" #header>
              <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить" outlined
                      @click="isModelAddDrawerOpen = true"/>
            </template>
          </Select>
          <FormError :errors="v$.modelId.$errors"/>
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

  <AddModelDrawer :visible="isModelAddDrawerOpen" @onAdd="onModelAdd" @onClose="isModelAddDrawerOpen = false"/>
</template>