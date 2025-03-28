<script setup>
import {useAuthStore} from "@stores";
import {useVuelidate} from '@vuelidate/core';
import moment from "moment";
import {email, helpers, required, minLength, sameAs} from '@vuelidate/validators';
import {computed, reactive, ref, unref, onMounted} from "vue";
import Divider from "primevue/divider";
import FormError from "@components/Inputs/FormError.vue";
import DirectoryService from "@services/DirectoryService.js";
import DatePickerWithMask from "@components/Inputs/DatePickerWithMask.vue";

const {user} = useAuthStore();

const props = defineProps({
  sending: {
    type: Boolean,
    required: false,
    default: false
  },
  item: {
    type: Object,
    required: true
  }
});
const emit = defineEmits(['onSubmit', 'onArchive', 'onDelete']);

const onlyNumbers = helpers.regex(/^\d+$/);

const state = reactive({
  companyId: user.company_id,
  full_name: props.item.full_name,
  short_name: props.item.short_name,
  registration_date: props.item.registration_date ? moment(props.item.registration_date).format('DD.MM.YYYY') : null,
  ogrn: props.item.ogrn,
  ogrnip: props.item.ogrnip,
  inn: props.item.inn,
  kpp: props.item.kpp,
  legal_address: props.item.legal_address,
  index_legal_address: props.item.index_legal_address,
  address: props.item.address,
  index_address: props.item.index_address,
  manager_position: props.item.manager_position,
  manager_fio: props.item.manager_fio,
  contact_fio: props.item.contact_fio,
  contact_phone: props.item.contact_phone,
  bookkeeper_fio: props.item.bookkeeper_fio,
  rs_legal_person: props.item.rs_legal_person,
  bank: props.item.bank,
  bank_bik: props.item.bank_bik,
  bank_ks: props.item.bank_ks,
  active: parseInt(props.item.status) === 1,
  is_lessor: parseInt(props.item.is_lessor) === 1,
  archive: parseInt(props.item.archive),
});
const rules = computed(() => {
  return {
    full_name: {
      required: helpers.withMessage("Полное наименование должно быть заполнено", required),
      $lazy: true
    },
    ogrn: {
      onlyNumbers: helpers.withMessage("ОГРН должно содержать только цифры", onlyNumbers),
      $lazy: true
    },
    ogrnip: {
      onlyNumbers: helpers.withMessage("ОГРНИП должно содержать только цифры", onlyNumbers),
      $lazy: true
    },
    inn: {
      onlyNumbers: helpers.withMessage("ИНН должно содержать только цифры", onlyNumbers),
      $lazy: true
    },
    kpp: {
      onlyNumbers: helpers.withMessage("КПП должно содержать только цифры", onlyNumbers),
      $lazy: true
    },
    index_legal_address: {
      onlyNumbers: helpers.withMessage("Индекс Юр адреса должен содержать только цифры", onlyNumbers),
      $lazy: true
    },
    index_address: {
      onlyNumbers: helpers.withMessage("Индекс Почтового адреса должен содержать только цифры", onlyNumbers),
      $lazy: true
    },
    rs_legal_person: {
      onlyNumbers: helpers.withMessage("Расчетный счет юр лица должен содержать только цифры", onlyNumbers),
      $lazy: true
    },
    bank_bik: {
      onlyNumbers: helpers.withMessage("БИК Банка должен содержать только цифры", onlyNumbers),
      $lazy: true
    },
    bank_ks: {
      onlyNumbers: helpers.withMessage("Корреспондентский счет Банка должен содержать только цифры", onlyNumbers),
      $lazy: true
    }
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
  <Card class="xl:w-8/12 w-full">
    <template #title>Редактирование юр лица <Badge v-if="state.archive === 1" value="Архив"></Badge></template>
    <template #content>
      <form @submit.prevent="onFormSubmit">
        <div class="grid gap-4 my-4 sm:grid-cols-1">
          <!-- Полное наименование* -->
          <div>
            <label for="full_name"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Полное наименование*</label>
            <InputText v-model="state.full_name" inputId="full_name" fluid />
            <FormError :errors="v$.full_name.$errors"/>
          </div>
          <!-- Является арендодателем? -->
          <div class="flex items-center">
            <input id="active" type="checkbox"
                   v-model="state.is_lessor"
                   class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
            <label for="active" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
              Является арендодателем?
            </label>
          </div>
          <!-- ОГРН -->
          <div>
            <label for="ogrn"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ОГРН</label>
            <InputText v-model="state.ogrn" inputId="ogrn" fluid />
            <FormError :errors="v$.ogrn.$errors"/>
          </div>
          <!-- Краткое наименование -->
          <div>
            <label for="short_name"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Краткое наименование</label>
            <InputText v-model="state.short_name" inputId="short_name" fluid />
          </div>
          <!-- Дата регистрации -->
          <div>
            <label for="registration_date"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата регистрации</label>
            <div>
              <DatePickerWithMask :value="state.registration_date" @onChange="e => state.registration_date = e" />
            </div>
          </div>
          <!-- ОГРНИП -->
          <div>
            <label for="ogrnip"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ОГРНИП</label>
            <InputText v-model="state.ogrnip" inputId="ogrnip" fluid />
            <FormError :errors="v$.ogrnip.$errors"/>
          </div>
          <!-- ИНН -->
          <div>
            <label for="inn"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ИНН</label>
            <InputText v-model="state.inn" inputId="inn" fluid />
            <FormError :errors="v$.inn.$errors"/>
          </div>
          <!-- КПП -->
          <div>
            <label for="kpp"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">КПП</label>
            <InputText v-model="state.kpp" inputId="inn" fluid />
            <FormError :errors="v$.kpp.$errors"/>
          </div>
          <!-- Юридический адрес -->
          <div>
            <label for="legal_address"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Юридический адрес</label>
            <InputText v-model="state.legal_address" inputId="legal_address" fluid />
          </div>
          <!-- Индекс Юр адреса -->
          <div>
            <label for="index_legal_address"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Индекс Юр адреса</label>
            <InputText v-model="state.index_legal_address" inputId="index_legal_address" fluid />
            <FormError :errors="v$.index_legal_address.$errors"/>
          </div>
          <!-- Почтовый адрес -->
          <div>
            <label for="address"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Почтовый адрес</label>
            <InputText v-model="state.address" inputId="address" fluid />
          </div>
          <!-- Индекс Почтового адреса -->
          <div>
            <label for="index_address"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Индекс Почтового адреса</label>
            <InputText v-model="state.index_address" inputId="index_address" fluid />
            <FormError :errors="v$.index_address.$errors"/>
          </div>
          <!-- Должность руководителя -->
          <div>
            <label for="manager_position"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Должность руководителя</label>
            <InputText v-model="state.manager_position" inputId="manager_position" fluid />
          </div>
          <!-- ФИО руководителя -->
          <div>
            <label for="manager_fio"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ФИО руководителя</label>
            <InputText v-model="state.manager_fio" inputId="manager_fio" fluid />
          </div>
          <!-- ФИО контактного лица -->
          <div>
            <label for="contact_fio"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ФИО контактного лица</label>
            <InputText v-model="state.contact_fio" inputId="contact_fio" fluid />
          </div>
          <!-- Телефон контактного лица -->
          <div>
            <label for="contact_phone"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Телефон контактного лица</label>
            <InputMask id="contact_phone" v-model="state.contact_phone" mask="9(999) 999-9999" placeholder="9(999) 999-9999" fluid/>
          </div>
          <!-- ФИО Бухгалтера -->
          <div>
            <label for="bookkeeper_fio"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ФИО Бухгалтера</label>
            <InputText v-model="state.bookkeeper_fio" inputId="bookkeeper_fio" fluid />
          </div>
          <!-- Расчетный счет юр лица -->
          <div>
            <label for="rs_legal_person"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Расчетный счет юр лица</label>
            <InputText v-model="state.rs_legal_person" inputId="rs_legal_person" fluid />
            <FormError :errors="v$.rs_legal_person.$errors"/>
          </div>
          <!-- Банк -->
          <div>
            <label for="bank"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Банк</label>
            <InputText v-model="state.bank" inputId="bank" fluid />
          </div>
          <!-- БИК Банка -->
          <div>
            <label for="bank_bik"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">БИК Банка</label>
            <InputText v-model="state.bank_bik" inputId="bank_bik" fluid />
            <FormError :errors="v$.bank_bik.$errors"/>
          </div>
          <!-- Корреспондентский счет Банка -->
          <div>
            <label for="bank_ks"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Корреспондентский счет Банка</label>
            <InputText v-model="state.bank_ks" inputId="bank_ks" fluid />
            <FormError :errors="v$.bank_ks.$errors"/>
          </div>

          <Divider type="dashed"/>
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
        <p v-for="error of v$.$errors" :key="error.$uid" class="text-red-500">
          {{ error.$message }}
        </p>
        <div v-if="user.access.clients === 2 && state.archive === 0">
          <Button type="submit" icon="pi pi-save" label="Сохранить" :loading="sending" outlined/>
          <Button icon="pi pi-trash" label="В архив" class="ml-4" severity="secondary" :loading="sending"
                  @click="emit('onArchive');" outlined/>
          <Button v-if="user.id === 1" icon="pi pi-trash" label="Удалить" class="ml-4" severity="danger" :loading="sending"
                  @click="emit('onDelete');" outlined/>
        </div>
        <div v-if="state.archive === 1">
          <Tag severity="warn" value="Юр лицо находится в архиве."/>
          <Button v-if="user.id === 1" icon="pi pi-trash" label="Удалить" class="ml-4" severity="danger" :loading="sending"
                  @click="emit('onDelete');" outlined/>
        </div>
      </form>
    </template>
  </Card>
</template>