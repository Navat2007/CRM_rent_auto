<script setup>
import {computed, onMounted, reactive, ref, unref} from "vue";
import Divider from "primevue/divider";
import {useAuthStore} from "@stores";
import {helpers, minValue, required} from "@vuelidate/validators";
import {useVuelidate} from "@vuelidate/core";
import FormError from "@components/Inputs/FormError.vue";
import PopUpAddDirectoryCarBrand from "@components/Popups/PopUpAddDirectoryCarBrand.vue";
import DirectoryService from "@services/DirectoryService.js";

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
  item: {
    type: Object,
    required: true
  }
})
const emit = defineEmits(['onSubmit', 'onDelete']);

const loadingBrands = ref(true);
const brands = ref([]);
const isBrandsAddModalOpen = ref(false);

const state = reactive({
  companyId: user.company_id,
  name: props.item.name,
  brandId: parseInt(props.item.brand) || 0,
  active: props.item.active,
});
const rules = computed(() => {
  return {
    name: {
      required: helpers.withMessage("Название должно быть заполнено", required),
      $lazy: true
    },
    brandId: {
      required: helpers.withMessage("Нужно выбрать марку авто", required),
      minValue: helpers.withMessage("Нужно выбрать марку авто", minValue(1)),
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

const onBrandAdd = async (id) => {
  loadingBrands.value = true;
  await fetchDirectoryBrands();
  state.brandId = id;
}

async function fetchDirectoryBrands() {
  brands.value = (await DirectoryService.getAll({directory: 'directory_car_brands', company_id: user.company_id})).filter(item => item.archive === "Активен");
  loadingBrands.value = false;
}

onMounted(() => {
  fetchDirectoryBrands();
});
</script>

<template>
  <Card class="w-full lg:w-2/3">
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
          <!-- Марка авто -->
          <div>
            <label for="brand"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Марка авто*</label>
            <Select v-model="state.brandId" :loading="loadingBrands" :options="brands"
                    optionLabel="name"
                    optionValue="id" placeholder="Выберите марку авто" showClear filter class="w-full">
              <template v-if="user.access.directory === 2" #header>
                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить" outlined
                        @click="isBrandsAddModalOpen = true"/>
              </template>
            </Select>
            <FormError :errors="v$.brandId.$errors"/>
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
        <Divider v-if="user.access.directory === 2" type="dashed"/>
        <Button v-if="user.access.directory === 2" type="submit" icon="pi pi-save" label="Сохранить" outlined />
        <Button class="ml-4" v-if="user.id === 1" @click="emit('onDelete')" icon="pi pi-trash" label="Удалить" outlined severity="danger" />
      </form>
    </template>
  </Card>
  <PopUpAddDirectoryCarBrand :visible="isBrandsAddModalOpen" @on-add="onBrandAdd"
                             @on-close="isBrandsAddModalOpen = false"/>
</template>