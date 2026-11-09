import { ref } from "vue";

export const loading = ref(false);
export const progress = ref(0);

export function startLoading() {
  loading.value = true;
  progress.value = 0;
  progressing();
}

export function stopLoading() {
  progress.value = 100;
  setTimeout(() => {
    loading.value = false;
    progress.value = 0;
  }, 325);
}

function progressing() {
  let step = 0.5;
  const interval = setInterval(() => {
    if (progress.value < 96) {
      progress.value += step;
      step += 0.5;
    } else {
      clearInterval(interval);
    }
  }, 100);
}
