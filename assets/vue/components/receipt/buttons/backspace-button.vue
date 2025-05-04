<template>
  <div
      class="backspace-button d-flex align-items-center justify-content-around"
      @click="backspaceClicked"
      :class="{'mousedown': isMouseDown, 'min-width': !useLandscapeMode}"
      ref="button"
  >
    <span class="fa-solid fa-2xl fa-backspace"></span>
  </div>
</template>

<script setup lang="ts">
import {ref, useTemplateRef} from "vue";
import {onLongPress, useMousePressed} from '@vueuse/core'
import {useSound} from "@vueuse/sound";
import buttonSound from '../../../../sounds/delete.mp3'
import warningSound from '../../../../sounds/warning.wav'

const {play} = useSound(buttonSound)
const {play: playWarning} = useSound(warningSound)

const props = defineProps({
  useLandscapeMode: {type: Boolean, required: true},
  buttonSound: {type: Boolean, required: true},
})
const emit = defineEmits(['create-new-receipt', 'backspaceClicked'])

const backspaceButton = useTemplateRef<HTMLLIElement>('button')
const isMouseDown = ref<boolean>(false);
const longPressDelay: number = 1000; // 1.5s
const longPressDelayWithUnit: string = `${longPressDelay}ms`

function backspaceClicked() {
  if (props.buttonSound) {
    play()
  }
  emit('backspaceClicked')
}

useMousePressed({
  target: backspaceButton,
  onPressed: () => isMouseDown.value = true,
  onReleased: () => isMouseDown.value = false,
})
onLongPress(backspaceButton, onLongPressCallbackHook, {
  modifiers: {prevent: false},
  delay: longPressDelay * 2.0,
})

function onLongPressCallbackHook(): void {
  isMouseDown.value = false
  if (props.buttonSound) {
    playWarning()
  }
  emit('create-new-receipt')
}
</script>

<style scoped lang="scss">
.backspace-button {
  background-color: var(--bs-dark-bg-subtle);
  border-right: 0.05em solid var(--bs-dark);
  cursor: pointer;

  &.min-width {
    width: 20vw;
    min-width: 4rem;
  }

  &:active, &:focus, &:hover {
    background-color: var(--bs-warning);
    color: var(--bs-danger);
  }

  &.mousedown {
    transition: background-color v-bind(longPressDelayWithUnit) ease-in, color v-bind(longPressDelayWithUnit) ease-in, box-shadow v-bind(longPressDelayWithUnit) ease-in;
    background-color: var(--bs-danger) !important;
    color: var(--bs-dark) !important;
    box-shadow: 0 0 1rem rgba(var(--bs-danger-rgb, 1.0)), 0 0 3rem rgba(var(--bs-danger-rgb, 0.8));
  }
}
</style>
