<!-- <template>
  <Teleport to="body">
    <div
      class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[300] pointer-events-none"
    >
      <Transition name="fade">
        <div
          v-if="visible"
          :class="typeClass"
          class="px-6 py-3 rounded-full text-white shadow-2xl flex items-center gap-3 text-sm font-medium pointer-events-auto"
        >
          <i :data-lucide="typeIcon" class="w-5 h-5" :class="iconClass"></i>
          {{ message }}
        </div>
      </Transition>
    </div>
  </Teleport>
</template>

<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
  message: { type: String, default: "" },
  type: { type: String, default: "success" }, // success | error | warning
  show: { type: Boolean, default: false },
});

const emit = defineEmits(["update:show"]);
const visible = ref(false);
let timer = null;

watch(
  () => props.show,
  (val) => {
    if (val) {
      visible.value = true;
      clearTimeout(timer);
      timer = setTimeout(() => {
        visible.value = false;
        emit("update:show", false);
      }, 3000);
    } else {
      visible.value = false;
    }
  },
);

const typeClass = computed(
  () =>
    ({
      success: "bg-slate-900",
      error: "bg-rose-600",
      warning: "bg-amber-500",
    })[props.type],
);

const typeIcon = computed(
  () =>
    ({
      success: "check-circle",
      error: "alert-circle",
      warning: "alert-triangle",
    })[props.type],
);

const iconClass = computed(
  () =>
    ({
      success: "text-emerald-400",
      error: "text-white",
      warning: "text-white",
    })[props.type],
);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition:
    opacity 0.2s,
    transform 0.2s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
</style> -->
