<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  platform: {
    type: Object,
    required: true
  },
  links: {
    type: Array,
    default: () => [],
    required: false
  },
  image: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <div class="p-5 bg-white rounded-3xl shadow justify-start items-start gap-5 inline-flex">
    <img class="rounded-2xl" :src="image" />
    <div class="grow shrink basis-0 self-stretch flex-col justify-start items-start gap-2 inline-flex">
      <div class="self-stretch grow shrink basis-0 flex-col justify-start items-start gap-4 flex">
        <div class="justify-start items-start gap-2 inline-flex">
          <div class="px-3.5 py-2 bg-slate-200 rounded-lg justify-center items-center gap-2 flex">
            <component :is="platform.icon" class="w-4 h-4 relative" />
            <div class="text-gray-600 text-sm font-normal font-['Inter']">{{ platform.title }}</div>
          </div>
        </div>
        <div class="self-stretch grow shrink basis-0 flex-col justify-start items-start gap-2 flex">
          <div class="self-stretch text-gray-800 text-2xl font-semibold font-['Inter']">{{ title }}</div>
          <div class="self-stretch grow shrink basis-0 text-gray-600 text-base font-normal font-['Inter']">{{ description }}</div>
        </div>
      </div>
      <div class="self-stretch py-2 justify-start items-start gap-4 inline-flex">
        <div class="w-6 h-6 relative"></div>
        <div class="w-6 h-6 p-0.5 justify-center items-center flex">
          <img class="w-5 h-5 rounded-sm" src="https://via.placeholder.com/20x20" />
        </div>
        <div class="w-6 h-6 relative"></div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.card {
  @apply flex flex-col lg:flex-row items-center p-5 gap-5 bg-gray-50 rounded-3xl shadow hover:shadow-lg dark:shadow-gray-600 dark:hover:shadow-gray-700 dark:bg-gray-800 dark:border-gray-700;
}

.logo {
  border-radius: 20px;

  @apply w-full lg:w-1/3 lg:min-h-[228px] lg:min-w-[228px] object-cover;
}

.platform {
  width: 87px;
  height: 33px;
;
  @apply flex mt-4 lg:mt-0 text-gray-500 dark:text-gray-400 items-center gap-2 p-2 rounded-lg bg-gray-200 dark:bg-gray-600;
}
</style>