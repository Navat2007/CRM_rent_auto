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
const emit = defineEmits(['onSubmit', 'onDelete']);

const genders = ref([
  {label: 'Мужской', value: 0,},
  {label: 'Женский', value: 1},
]);
const positions = ref([]);
const loadingPositions = ref(true);
const age = computed(() => {
  const declOfNum = (number, words) => {
    return words[(number % 100 > 4 && number % 100 < 20) ? 2 : [2, 0, 1, 1, 1, 2][(number % 10 < 5) ? Math.abs(number) % 10 : 5]];
  }

  const today = new Date();
  const birthDate = moment(state.birthday, 'DD.MM.YYYY').toDate();
  let age = today.getFullYear() - birthDate.getFullYear();

  if (today.getMonth() < birthDate.getMonth() ||
      (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())) {
    age--;
  }

  return state.birthday ? age + ' ' + declOfNum(age, ['год', 'года', 'лет']) : null;
});
const zodiac = computed(() => {
  function getZodiacSign(date) {
    const month = date.getMonth() + 1;
    const day = date.getDate();

    if (month == 1 && day >= 20 || month == 2 && day <= 18) return "Водолей";
    else if (month == 2 && day >= 19 || month == 3 && day <= 20) return "Рыбы";
    else if (month == 3 && day >= 21 || month == 4 && day <= 19) return "Овен";
    else if (month == 4 && day >= 20 || month == 5 && day <= 20) return "Телец";
    else if (month == 5 && day >= 21 || month == 6 && day <= 21) return "Близнецы";
    else if (month == 6 && day >= 22 || month == 7 && day <= 22) return "Рак";
    else if (month == 7 && day >= 23 || month == 8 && day <= 22) return "Лев";
    else if (month == 8 && day >= 23 || month == 9 && day <= 22) return "Дева";
    else if (month == 9 && day >= 23 || month == 10 && day <= 22) return "Весы";
    else if (month == 10 && day >= 23 || month == 11 && day <= 21) return "Скорпион";
    else if (month == 11 && day >= 22 || month == 12 && day <= 21) return "Стрелец";
    else if (month == 12 && day >= 22 || month == 1 && day <= 19) return "Козерог";
  }

  const birthDate = moment(state.birthday, 'DD.MM.YYYY').toDate();
  return state.birthday ? ' (' + getZodiacSign(birthDate) + ')' : null;
});
const passwordDisabled = ref(true);

const lettersAndDash = helpers.regex(/^[a-zA-Zа-яА-Я-]*$/);

const state = reactive({
  companyId: user.company_id,
  email: props.item.email,
  firstName: props.item.first_name,
  lastName: props.item.second_name,
  patronym: props.item.middle_name,
  password: '',
  confirmPassword: '',
  birthday: props.item.birth_date ? moment(props.item.birth_date).format('DD.MM.YYYY'): null,
  gender: props.item.gender ? parseInt(props.item.gender) : 0,
  phone: props.item.phone,
  position: parseInt(props.item.user_type),
  snils: props.item.snils,
  inn: props.item.inn,
  hireDate: props.item.hire_date ? moment(props.item.hire_date).format('DD.MM.YYYY') : null,
  actsOnBasis: props.item.acts_on_basis,
  rate: props.item.rate,
  active: parseInt(props.item.status) === 1,
});
const rules = computed(() => {
  return {
    email: {
      required: helpers.withMessage("Поле должно быть заполнено", required),
      email: helpers.withMessage("Введите корректный email", email),
      $lazy: true
    },
    firstName: {
      required: helpers.withMessage("Поле должно быть заполнено", required),
      lettersAndDash: helpers.withMessage("Только буквы и тире", lettersAndDash),
      $lazy: true
    },
    lastName: {
      required: helpers.withMessage("Поле должно быть заполнено", required),
      lettersAndDash: helpers.withMessage("Только буквы и тире", lettersAndDash),
      $lazy: true
    },
    patronym: {
      lettersAndDash: helpers.withMessage("Только буквы и тире", lettersAndDash),
    },
    password: {
      minLength: helpers.withMessage("Минимальная длина пароля - 6 символов", minLength(6)),
      $lazy: true
    },
    confirmPassword: {
      sameAsPassword: helpers.withMessage("Пароли не совпадают", sameAs(state.password)),
      $lazy: true
    },
    birthday: {},
    hireDate: {},
    actsOnBasis: {},
    rate: {},
  }
});
const v$ = useVuelidate(rules, state);

const onFormSubmit = async (e) => {
  const isFormCorrect = await unref(v$).$validate();

  if (isFormCorrect) {
    emit('onSubmit', state);
  }
};

async function fetchPositions() {
  positions.value = (await DirectoryService.getPositions(user.company_id)).filter(position => position.archive === "Активен");
  loadingPositions.value = false;
}

onMounted(() => {
  fetchPositions();
});
</script>

<template>
  <Card class="w-full lg:w-2/3">
    <template #title>Редактирование сотрудника</template>
    <template #content>
      <form @submit.prevent="onFormSubmit" autocomplete="off">
        <Tabs value="0" scrollable>
          <TabList>
            <Tab value="0" class="flex gap-2">Основная информация<Badge severity="danger" value="2" /></Tab>
            <Tab value="1" class="flex gap-2">Паспорт<Badge severity="danger" value="1" /></Tab>
            <Tab value="2" class="flex gap-2">Водительское удостоверение</Tab>
            <Tab value="3" class="flex gap-2">Прочие документы</Tab>
            <Tab value="4" class="flex gap-2">Права доступа</Tab>
          </TabList>
          <TabPanels>
            <TabPanel value="0">
              <div class="grid gap-4 my-4 sm:grid-cols-1">
                <!-- Email -->
                <div>
                  <label for="email"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email*</label>
                  <input
                      v-model="state.email"
                      type="email" id="email"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                  <FormError :errors="v$.email.$errors"/>
                </div>
                <!-- Фамилия -->
                <div>
                  <label for="firstName"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Фамилия*</label>
                  <input
                      v-model="state.lastName"
                      type="text" id="lastName"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                  <FormError :errors="v$.lastName.$errors"/>
                </div>
                <!-- Имя -->
                <div>
                  <label for="firstName"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Имя*</label>
                  <input
                      v-model="state.firstName"
                      type="text" id="firstName"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                  <FormError :errors="v$.firstName.$errors"/>
                </div>
                <!-- Отчество -->
                <div>
                  <label for="patronym"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Отчество</label>
                  <input
                      v-model="state.patronym"
                      type="text" id="patronym"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                  <FormError :errors="v$.patronym.$errors"/>
                </div>
                <Divider type="dashed"/>
                <!-- Дата рождения -->
                <div>
                  <label for="birthday"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата рождения</label>
                  <div>
                    <DatePickerWithMask :value="state.birthday" @onChange="e => state.birthday = e" />
                    <p class="mt-2">{{ age }}{{ zodiac }}</p>
                  </div>
                  <FormError :errors="v$.birthday.$errors"/>
                </div>
                <!-- Пол -->
                <div>
                  <label for="gender"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Пол</label>
                  <Select v-model="state.gender" :options="genders" :modelValue="state.gender" optionLabel="label"
                          optionValue="value" placeholder="Выберите пол" class="w-full"/>
                </div>
                <!-- Телефон -->
                <div>
                  <label for="phone"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Телефон</label>
                  <InputMask id="phone" v-model="state.phone" mask="(999) 999-9999" placeholder="(999) 999-9999" fluid/>
                </div>
                <!-- Должность -->
                <div>
                  <label for="position"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Должность</label>
                  <Select v-model="state.position" :loading="loadingPositions" :options="positions" optionLabel="name"
                          optionValue="id" placeholder="Выберите должность" showClear class="w-full"/>
                </div>
                <!-- СНИЛС -->
                <div>
                  <label for="snils"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">СНИЛС</label>
                  <InputMask id="snils" v-model="state.snils" :modelValue="state.snils" mask="999-999-999 99"
                             placeholder="999-999-999 99" fluid/>
                </div>
                <!-- ИНН -->
                <div>
                  <label for="inn"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ИНН</label>
                  <InputMask id="inn" v-model="state.inn" :modelValue="state.inn" mask="999999999999"
                             placeholder="999999999999" fluid/>
                </div>
                <!-- Дата найма -->
                <div>
                  <label for="hireDate"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата найма</label>
                  <DatePickerWithMask :value="state.hireDate" @onChange="e => state.hireDate = e" />
                  <FormError :errors="v$.hireDate.$errors"/>
                </div>
                <!-- Действует на основании -->
                <div>
                  <label for="actsOnBasis"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Действует на основании</label>
                  <input
                      v-model="state.actsOnBasis"
                      type="text" id="actsOnBasis"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                  <FormError :errors="v$.actsOnBasis.$errors"/>
                </div>
                <!-- Ставка -->
                <div>
                  <label for="rate"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ставка</label>
                  <input
                      v-model="state.rate"
                      type="text" id="rate"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                  <FormError :errors="v$.rate.$errors"/>
                </div>
                <Divider type="dashed"/>
                <div v-if="passwordDisabled">
                  <Button icon="pi pi-lock" label="Изменить пароль" severity="danger" @click="passwordDisabled = false"/>
                </div>
                <!-- Пароль -->
                <div>
                  <label for="password"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Новый пароль</label>
                  <Password inputId="password" v-model="state.password" placeholder="Введите пароль" :toggleMask="true"
                            :feedback="false" class="w-full mb-3" inputClass="w-full" :disabled="passwordDisabled"/>
                  <FormError :errors="v$.password.$errors"/>
                </div>
                <!-- Подтвердить пароль -->
                <div>
                  <label for="confirmPassword"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Подтвердить пароль</label>
                  <Password inputId="confirmPassword" v-model="state.confirmPassword" placeholder="Введите пароль"
                            :toggleMask="true"
                            :feedback="false" class="w-full mb-3" inputClass="w-full" :disabled="passwordDisabled"/>
                  <FormError :errors="v$.confirmPassword.$errors"/>
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
            </TabPanel>
            <TabPanel value="1">

            </TabPanel>
            <TabPanel value="2">

            </TabPanel>
            <TabPanel value="3">

            </TabPanel>
            <TabPanel value="4">

            </TabPanel>
          </TabPanels>
        </Tabs>

        <Divider type="dashed"/>
        <Button type="submit" icon="pi pi-plus" label="Сохранить" :loading="sending" outlined/>
        <Button icon="pi pi-trash" label="В архив" class="ml-4" severity="secondary" :loading="sending"
                @click="emit('onDelete');" outlined/>
      </form>
    </template>
  </Card>
</template>