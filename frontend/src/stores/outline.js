import { ref, watch } from "vue";
import { defineStore } from "pinia";

export const useOutlineStore = defineStore("outline", () => {
  const outline = ref(localStorage.getItem("scaffold") === "true");

  const toggleOutline = () => {
    outline.value = !outline.value;
  };

  watch(
    outline,
    (value) => {
      localStorage.setItem("scaffold", value ? "true" : "false");
      document.body.classList.toggle("outline", value);
    },
    { immediate: true },
  );

  return {
    outline,
    toggleOutline,
  };
});
