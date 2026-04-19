<script setup>
import { ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({
    property: { type: Object, required: true },
    statusOptions: { type: Array, default: () => [] },
    getStatusVariant: { type: Function, required: true },
    capitalizeText: { type: Function, required: true },
});

const emit = defineEmits(['select']);

const open = ref(false);
const anchorRef = ref(null);
const menuRef = ref(null);
const menuStyle = ref({});

const MENU_GAP = 4;

function positionMenu() {
    const a = anchorRef.value?.getBoundingClientRect();
    if (!a) return;

    const menuEl = menuRef.value;
    const h = menuEl?.offsetHeight ?? 120;
    const w = Math.max(a.width, 160);

    let top = a.bottom + MENU_GAP;
    if (top + h > window.innerHeight - 8) {
        top = Math.max(8, a.top - h - MENU_GAP);
    }

    let left = a.left;
    if (left + w > window.innerWidth - 8) {
        left = Math.max(8, window.innerWidth - w - 8);
    }

    menuStyle.value = {
        position: 'fixed',
        top: `${top}px`,
        left: `${left}px`,
        minWidth: `${w}px`,
        zIndex: 1080,
    };
}

function toggle(e) {
    e?.stopPropagation();
    e?.preventDefault();
    open.value = !open.value;
}

function close() {
    open.value = false;
}

function onSelect(opt) {
    emit('select', opt.value);
    close();
}

function onDocPointerDown(e) {
    if (!open.value) return;
    const t = e.target;
    if (anchorRef.value?.contains(t) || menuRef.value?.contains(t)) return;
    close();
}

function onScrollOrResize() {
    if (open.value) positionMenu();
}

watch(open, (v) => {
    if (v) {
        nextTick(() => {
            positionMenu();
            nextTick(() => positionMenu());
        });
    }
});

onMounted(() => {
    document.addEventListener('pointerdown', onDocPointerDown, true);
    window.addEventListener('scroll', onScrollOrResize, true);
    window.addEventListener('resize', onScrollOrResize);
});

onBeforeUnmount(() => {
    document.removeEventListener('pointerdown', onDocPointerDown, true);
    window.removeEventListener('scroll', onScrollOrResize, true);
    window.removeEventListener('resize', onScrollOrResize);
});
</script>

<template>
    <div class="property-status-select">
        <button
            ref="anchorRef"
            type="button"
            class="property-status-select__toggle btn btn-link p-0 border-0 shadow-none text-decoration-none"
            :aria-expanded="open"
            aria-haspopup="listbox"
            @click="toggle"
        >
            <span
                class="badge text-white d-inline-flex align-items-center"
                :class="`bg-${getStatusVariant(property.status)}`"
            >
                {{ capitalizeText(property.status) }}
                <i
                    class="fas fa-chevron-down ms-2 property-status-select__chevron"
                    :class="{ 'property-status-select__chevron--open': open }"
                />
            </span>
        </button>

        <Teleport to="body">
            <Transition name="property-status-select-fade">
                <div
                    v-if="open"
                    ref="menuRef"
                    class="property-status-select__menu dropdown-menu show shadow py-1"
                    :style="menuStyle"
                    role="listbox"
                >
                    <button
                        v-for="opt in statusOptions"
                        :key="opt.value"
                        type="button"
                        class="dropdown-item d-flex align-items-center"
                        role="option"
                        @click="onSelect(opt)"
                    >
                        <span
                            class="badge text-white me-2"
                            :class="`bg-${getStatusVariant(opt.value)}`"
                        >
                            {{ opt.text }}
                        </span>
                    </button>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.property-status-select__toggle:focus {
    box-shadow: none;
}

.property-status-select__chevron {
    transition: transform 0.2s ease;
    font-size: 0.65em;
}

.property-status-select__chevron--open {
    transform: rotate(180deg);
}

.property-status-select-fade-enter-active,
.property-status-select-fade-leave-active {
    transition: opacity 0.18s ease;
}

.property-status-select-fade-enter-from,
.property-status-select-fade-leave-to {
    opacity: 0;
}
</style>
