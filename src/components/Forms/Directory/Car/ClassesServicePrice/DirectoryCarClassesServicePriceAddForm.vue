<script setup>
import Divider from "primevue/divider";
import {useAuthStore} from "@stores";
import {computed, onMounted, reactive, ref, unref} from "vue";
import {helpers, required, minValue} from "@vuelidate/validators";
import {useVuelidate} from "@vuelidate/core";
import FormError from "@components/Inputs/FormError.vue";
import DirectoryService from "@services/DirectoryService.js";
import PopUpAddDirectoryCarBrand from "@components/Popups/PopUpAddDirectoryCarBrand.vue";
import PopUpAddAdvertisingType from "@components/Popups/PopUpAddAdvertisingType.vue";
import AddClassDrawer from "@components/Drawers/Directory/Car/AddClassDrawer.vue";
import AddServicesDrawer from "@components/Drawers/Directory/AddServicesDrawer.vue";

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

const directoryClasses = ref([]);
const loadingDirectoryClasses = ref(true);
const isDirectoryClassesAddDrawerOpen = ref(false);

const directoryServices = ref([]);
const loadingDirectoryServices = ref(true);
const isDirectoryServicesAddDrawerOpen = ref(false);

const state = reactive({
  companyId: user.company_id,
  directory_car_classes_id: 0,
  directory_services_id: 0,
  price: 0,
  active: true
});
const rules = computed(() => {
  return {
    price: {
      required: helpers.withMessage("Цена должна быть заполнена", required),
      $lazy: true
    },
    directory_car_classes_id: {
      required: helpers.withMessage("Нужно выбрать класс авто", required),
      minValue: helpers.withMessage("Нужно выбрать класс авто", minValue(1)),
      $lazy: true
    },
    directory_services_id: {
      required: helpers.withMessage("Нужно выбрать услугу", required),
      minValue: helpers.withMessage("Нужно выбрать услугу", minValue(1)),
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

async function onDirectoryClassAdd(id) {
  loadingDirectoryClasses.value = true;
  await fetchDirectoryCarClasses();
  state.directory_car_classes_id = id;
  isDirectoryClassesAddDrawerOpen.value = false;
}
async function onDirectoryServiceAdd(id) {
  loadingDirectoryServices.value = true;
  await fetchDirectoryServices();
  state.directory_services_id = id;
  isDirectoryServicesAddDrawerOpen.value = false;
}

async function fetchDirectoryCarClasses() {
  directoryClasses.value = await DirectoryService.getAll({directory: 'directory_car_classes', company_id: user.company_id});
  directoryClasses.value = directoryClasses.value.filter(item => item.archive === "Активен");
  loadingDirectoryClasses.value = false;
}
async function fetchDirectoryServices() {
  directoryServices.value = await DirectoryService.getAll({directory: 'directory_services', company_id: user.company_id});
  directoryServices.value = directoryServices.value.filter(item => item.archive === "Активен");
  loadingDirectoryServices.value = false;
}

onMounted(() => {
  fetchDirectoryCarClasses();
  fetchDirectoryServices();
});
</script>

<template>
  <Card v-if="card" class="w-full lg:w-2/3">
    <template #title>{{title}}</template>
    <template #content>
      <form @submit.prevent="onFormSubmit">
        <div class="grid gap-4 mb-4 mt-2 sm:grid-cols-1">
          <!--  Класс авто -->
          <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Класс авто*</label>
            <Select v-model="state.directory_car_classes_id" :loading="loadingDirectoryClasses"
                    :options="directoryClasses"
                    optionLabel="name"
                    optionValue="id" placeholder="Выберите класс авто" showClear filter class="w-full">
              <template v-if="user.access.auto === 2" #header>
                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить" outlined
                        @click="isDirectoryClassesAddDrawerOpen = true"/>
              </template>
            </Select>
            <FormError :errors="v$.directory_car_classes_id.$errors"/>
          </div>
          <!--  Услуги при бронировании -->
          <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Услуги при бронировании*</label>
            <Select v-model="state.directory_services_id" :loading="loadingDirectoryServices"
                    :options="directoryServices"
                    optionLabel="name"
                    optionValue="id" placeholder="Выберите услугу" showClear filter class="w-full">
              <template v-if="user.access.auto === 2" #header>
                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить" outlined
                        @click="isDirectoryServicesAddDrawerOpen = true"/>
              </template>
            </Select>
            <FormError :errors="v$.directory_services_id.$errors"/>
          </div>
          <!-- Цена -->
          <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Цена*</label>
            <InputNumber id="price" v-model="state.price" :min="0" fluid/>
            <FormError :errors="v$.price.$errors"/>
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
        <!--  Класс авто -->
        <div>
          <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Класс авто*</label>
          <Select v-model="state.directory_car_classes_id" :loading="loadingDirectoryClasses"
                  :options="directoryClasses"
                  optionLabel="name"
                  optionValue="id" placeholder="Выберите класс авто" showClear filter class="w-full">
            <template v-if="user.access.auto === 2" #header>
              <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить" outlined
                      @click="isDirectoryClassesAddDrawerOpen = true"/>
            </template>
          </Select>
          <FormError :errors="v$.directory_car_classes_id.$errors"/>
        </div>
        <!--  Услуги при бронировании -->
        <div>
          <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Услуги при бронировании*</label>
          <Select v-model="state.directory_services_id" :loading="loadingDirectoryServices"
                  :options="directoryServices"
                  optionLabel="name"
                  optionValue="id" placeholder="Выберите услугу" showClear filter class="w-full">
            <template v-if="user.access.auto === 2" #header>
              <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить" outlined
                      @click="isDirectoryServicesAddDrawerOpen = true"/>
            </template>
          </Select>
          <FormError :errors="v$.directory_services_id.$errors"/>
        </div>
        <!-- Цена -->
        <div>
          <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Цена*</label>
          <InputNumber id="price" v-model="state.price" :min="0" fluid/>
          <FormError :errors="v$.price.$errors"/>
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

  <AddClassDrawer
      :visible="isDirectoryClassesAddDrawerOpen"
      @onAdd="onDirectoryClassAdd" @onClose="isDirectoryClassesAddDrawerOpen = false"
  />
  <AddServicesDrawer
      :visible="isDirectoryServicesAddDrawerOpen"
      @onAdd="onDirectoryServiceAdd" @onClose="isDirectoryServicesAddDrawerOpen = false"
  />
</template>