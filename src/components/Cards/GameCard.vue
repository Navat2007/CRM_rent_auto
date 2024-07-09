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
  <div class="card">
    <img class="logo" :src="image" :alt="title">
    <div class="grow shrink basis-0 self-stretch flex-col justify-start items-start gap-2 inline-flex">
      <div class="self-stretch grow shrink basis-0 flex-col justify-start items-start gap-4 flex">
        <!-- Platform chips -->
        <div class="platform">
          <component :is="platform.icon" class="h-4 w-4"/> {{ platform.title }}
        </div>
        <!-- Title -->
        <div class="flex text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ title }}</div>
        <!-- Description -->
        <div class="description">{{ description }}</div>
      </div>
      <!-- Links -->
      <div v-if="links" class="self-stretch py-2 justify-start items-start gap-4 inline-flex">
        <a v-for="link in links" :key="link.url" :href="link.url" target="_blank" class="link">
          <component :is="link.icon" class="h-6 w-6"/>
        </a>
      </div>
    </div>
  </div>
</template>

<style scoped>
.card {
  @apply flex flex-col lg:flex-row items-center p-5 gap-5 bg-gray-50 rounded-3xl duration-300 shadow hover:shadow-lg dark:shadow-gray-600 dark:hover:shadow-gray-700 dark:bg-gray-800 dark:border-gray-700;

  &:hover {
    --tw-scale-x: 1.02;
    --tw-scale-y: 1.02;
    transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
  }
}

.logo {
  @apply rounded-2xl w-full lg:w-1/3 lg:min-h-[228px] lg:min-w-[228px] object-cover;
}

.platform {
  @apply px-3.5 py-2 rounded-lg justify-center items-center gap-2 flex bg-slate-200 dark:bg-gray-600;
}

.description {
  @apply lg:line-clamp-2 font-normal text-gray-500 dark:text-gray-400;
}

.link {
  @apply cursor-pointer duration-300 hover:scale-150;
}
</style>