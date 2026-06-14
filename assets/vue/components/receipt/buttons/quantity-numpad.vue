<template>
  <div class="quantity-numpad">
    <div class="numpad-grid">
      <div v-for="key in keys" :key="key.label" class="numpad-key" :class="{'numpad-key--wide': key.wide}" @click="handleKey(key)">
        {{ key.label }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {type PropType} from 'vue'
import {useSound} from "@vueuse/sound";
import clickSound from '../../../../sounds/article.mp3'

const props = defineProps({
  modelValue: {type: Number as PropType<number | null>, default: null},
  buttonSound: {type: Boolean, required: true},
})
const emit = defineEmits<{
  'update:modelValue': [value: number | null]
}>()

const {play: playClick} = useSound(clickSound)

type Key = {label: string; digit: number; wide?: boolean}

const keys: Key[] = [
  {label: '7', digit: 7},
  {label: '8', digit: 8},
  {label: '9', digit: 9},
  {label: '4', digit: 4},
  {label: '5', digit: 5},
  {label: '6', digit: 6},
  {label: '1', digit: 1},
  {label: '2', digit: 2},
  {label: '3', digit: 3},
  {label: '0', digit: 0, wide: true},
]

function handleKey(key: Key): void {
  if (props.buttonSound) playClick()
  if (props.modelValue === null) {
    if (key.digit === 0) return // no leading zero
    emit('update:modelValue', key.digit)
  } else {
    const next = props.modelValue * 10 + key.digit
    if (next > 99) return
    emit('update:modelValue', next)
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

.numpad-key--wide {
  grid-column: 1 / -1;
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
