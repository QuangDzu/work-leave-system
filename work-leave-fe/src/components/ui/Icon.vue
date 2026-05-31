<template>
  <component :is="icon" v-bind="$attrs" />
</template>

<script setup>
import * as LucideIcons from "lucide-vue-next";
import { computed } from "vue";

const props = defineProps({
  name: { type: String, required: true },
});

const icon = computed(() => LucideIcons[props.name] || LucideIcons.HelpCircle);
</script>
