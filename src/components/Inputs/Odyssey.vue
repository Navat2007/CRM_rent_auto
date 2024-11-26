<script setup>
import {ref} from "vue";
import AlertModal from "@components/Modals/AlertModal.vue";
import OdysseyService from "@services/OdysseyService.js";

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  lastname: {
    type: String,
    required: true
  },
  firstname: {
    type: String,
    required: true
  },
  middlename: {
    type: String,
    required: false
  },
  birthday: {
    type: String,
    required: true
  },
  results: {
    type: Array,
    required: false,
    default: []
  },
  loadingResults: {
    type: Boolean,
    required: false,
    default: false
  }
});
const emit = defineEmits(['onResult']);

const sending = ref(false);
const error = ref('');
const isAlertModalOpen = ref(false);
const isFilledAlertModalOpen = ref(false);
const isHasResultAlertModalOpen = ref(false);
const isSendRequestAlertModalOpen = ref(false);

const check = () => {
  if(props.firstname === '' || props.lastname === '' || props.birthday === '' || props.birthday === null) {
    isFilledAlertModalOpen.value = true;
  }
  else if(props.results.length > 0) {
    isHasResultAlertModalOpen.value = true;
  }
  else {
    isSendRequestAlertModalOpen.value = true;
  }
}
const handleAcceptRepeat = () => {
  isHasResultAlertModalOpen.value = false;
  isSendRequestAlertModalOpen.value = true;
};
const handleSendRequest = () => {
  isSendRequestAlertModalOpen.value = false;
  sending.value = true;
  sendRequest();
};

async function sendRequest() {
  let result = await OdysseyService.sendRequest(props.lastname, props.firstname, props.middlename, props.birthday);

  if(!result){
    isAlertModalOpen.value = true;
    error.value = "Ошибка при отправке запроса!";
  }
  else if(result && result.status !== "ok"){
    isAlertModalOpen.value = true;
    error.value = result.status;
  }
  else {
    let scoring = 0;

    for(let item of result.data) {
      if(item['ИСТОЧНИК'] === "Скоринг"){
        scoring = item['ОБЩИЙ ПОКАЗАТЕЛЬ'];
        break;
      }
    }

    result = {
      user_id: props.id,
      scoring: scoring,
      url: result.url,
      f: props.lastname,
      i: props.firstname,
      o: props.middlename,
      birthday: props.birthday
    };

    await OdysseyService.saveResult(result);

    emit('onResult', result);
  }

  sending.value = false;
}
</script>

<template>
  <div>
    <Button icon="pi pi-upload" label="Проверить в odyssey" :loading="sending || loadingResults" outlined
            @click="check"/>
  </div>

  <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  <AlertModal :isOpen="isFilledAlertModalOpen" @close="isFilledAlertModalOpen = false"
              title="Проверьте заполненность полей:" info
  >
    <template #body>
      <p v-if="props.lastname === ''">Фамилия не заполнена!</p>
      <p v-if="props.firstname === ''">Имя не заполнено!</p>
      <p v-if="props.birthday === '' || props.birthday === null">Дата рождения не заполнена!</p>
    </template>
  </AlertModal>
  <AlertModal
      :isOpen="isHasResultAlertModalOpen"
      @close="isHasResultAlertModalOpen = false"
      @accept="handleAcceptRepeat"
      title="По данному клиенту уже делали проверку. Повторить?"
      withButtons info/>
  <AlertModal
      :invert-buttons="true"
      :isOpen="isSendRequestAlertModalOpen"
      @close="isSendRequestAlertModalOpen = false"
      @accept="handleSendRequest"
      title="Подтвердите отправку запроса для проверки клиента"
      withButtons info/>
</template>