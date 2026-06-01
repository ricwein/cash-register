<template>
  <div class="quantity-numpad">
    <div class="numpad-grid">
      <div v-for="key in keys" :key="key.label" class="numpad-key" @click="handleKey(key)">
        {{ key.label }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {type PropType} from 'vue'
import {useSound} from "@vueuse/sound";
import clickSound from '../../../../sounds/article.mp3'
import deleteSound from '../../../../sounds/delete.mp3'

const props = defineProps({
  modelValue: {type: Number as PropType<number | null>, default: null},
  buttonSound: {type: Boolean, required: true},
})
const emit = defineEmits<{
  'update:modelValue': [value: number | null]
}>()

const {play: playClick} = useSound(clickSound)
const {play: playDelete} = useSound(deleteSound)

type Key = {label: string; action: 'digit' | 'clear' | 'backspace'; digit?: number}

const keys: Key[] = [
  {label: '7', action: 'digit', digit: 7},
  {label: '8', action: 'digit', digit: 8},
  {label: '9', action: 'digit', digit: 9},
  {label: '4', action: 'digit', digit: 4},
  {label: '5', action: 'digit', digit: 5},
  {label: '6', action: 'digit', digit: 6},
  {label: '1', action: 'digit', digit: 1},
  {label: '2', action: 'digit', digit: 2},
  {label: '3', action: 'digit', digit: 3},
  {label: '×', action: 'clear'},
  {label: '0', action: 'digit', digit: 0},
  {label: '⌫', action: 'backspace'},
]

function handleKey(key: Key): void {
  if (key.action === 'digit') {
    if (props.buttonSound) playClick()
    const d = key.digit!
    if (props.modelValue === null) {
      if (d === 0) return // no leading zero
      emit('update:modelValue', d)
    } else {
      const next = props.modelValue * 10 + d
      if (next > 99) return
      emit('update:modelValue', next)
    }
  } else if (key.action === 'backspace') {
    if (props.buttonSound) playDelete()
    if (props.modelValue === null) return
    const next = Math.floor(props.modelValue / 10)
    emit('update:modelValue', next === 0 ? null : next)
  } else {
    if (props.buttonSound) playDelete()
    emit('update:modelValue', null)
  }
}
</script>

<style scoped lang="scss">
.quantity-numpad {
  border-top: 0.1rem solid var(--bs-dark);
  padding: 0.4rem;
}

.numpad-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.25rem;
}

.numpad-key {
  background-color: var(--bs-dark-bg-subtle);
  border: 0.05rem solid var(--bs-dark);
  border-radius: 0.25rem;
  cursor: pointer;
  text-align: center;
  padding: 0.4rem 0;
  font-size: 1.1rem;
  user-select: none;

  &:active, &:hover {
    background-color: var(--bs-secondary-bg);
  }
}
</style>
