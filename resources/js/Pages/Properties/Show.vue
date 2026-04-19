<script setup>
import { computed, ref, watch } from 'vue';
import Layout from '@/components/Layout.vue';
import { Link } from '@inertiajs/vue3';
import { capitalizeText } from '@/utils/helper';

const props = defineProps({
    property: { type: Object, required: true },
});

const gallery = computed(() =>
    Array.isArray(props.property.gallery) ? props.property.gallery : []
);

const isGalleryVisualType = (type) => {
    const t = (type ?? 'image').toString().toLowerCase();
    return t === 'image' || t === 'video';
};

const visualItems = computed(() =>
    gallery.value.filter((g) => Boolean(g?.url) && isGalleryVisualType(g.type))
);

const activeId = ref(null);

watch(
    visualItems,
    (items) => {
        if (!items.length) {
            activeId.value = null;
            return;
        }
        if (!items.some((g) => g.id === activeId.value)) {
            activeId.value = items[0].id;
        }
    },
    { immediate: true }
);

const activeItem = computed(() => visualItems.value.find((g) => g.id === activeId.value) ?? null);

const priceLabel = computed(() => {
    const n = Number(props.property.price);
    if (Number.isNaN(n)) return '—';
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(n);
});

const getStatusVariant = (status) => {
    if (status === 'approved') return 'success';
    if (status === 'rejected') return 'danger';
    if (status === 'pending') return 'warning';
    return 'secondary';
};
</script>

<template>
    <Layout :title="property.name">
        <div class="mb-3">
            <Link :href="route('properties.index')" class="text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Back to properties
            </Link>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="mb-1">{{ property.name }}</h2>
                <p class="text-muted mb-0" v-if="property.category?.name">
                    {{ property.category.name }}
                    <span v-if="property.listing_type" class="ms-2">
                        · {{ capitalizeText(property.listing_type) }}
                    </span>
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <b-badge :variant="getStatusVariant(property.status)">
                    {{ capitalizeText(property.status || '') }}
                </b-badge>
                <b-badge v-if="property.is_featured" variant="info">Featured</b-badge>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="ratio ratio-16x9 bg-light rounded-top overflow-hidden">
                            <img
                                v-if="activeItem && (activeItem.type || 'image').toString().toLowerCase() !== 'video'"
                                :src="activeItem.url"
                                :alt="property.name"
                                class="object-fit-contain w-100 h-100"
                            />
                            <video
                                v-else-if="activeItem && (activeItem.type || '').toString().toLowerCase() === 'video'"
                                :src="activeItem.url"
                                class="w-100 h-100"
                                controls
                                playsinline
                            />
                            <div
                                v-else
                                class="d-flex align-items-center justify-content-center text-muted small p-4"
                            >
                                No images or videos for this property yet.
                            </div>
                        </div>
                        <div
                            v-if="visualItems.length > 1"
                            class="d-flex flex-wrap gap-2 p-3 border-top bg-white rounded-bottom"
                        >
                            <button
                                v-for="item in visualItems"
                                :key="item.id"
                                type="button"
                                class="thumb-btn border rounded overflow-hidden p-0 bg-white"
                                :class="{ 'thumb-btn--active': item.id === activeId }"
                                @click="activeId = item.id"
                            >
                                <img
                                    v-if="(item.type || 'image').toString().toLowerCase() !== 'video'"
                                    :src="item.url"
                                    alt=""
                                    class="thumb-img"
                                />
                                <div
                                    v-else
                                    class="thumb-img d-flex align-items-center justify-content-center bg-dark text-white small"
                                >
                                    <i class="fas fa-film"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white fw-semibold">Details</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Price</span>
                            <span class="fw-medium">{{ priceLabel }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Area</span>
                            <span>{{ property.area != null && property.area !== '' ? property.area : '—' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <span class="text-muted">Location</span>
                            <span class="text-end text-break ms-3">{{ property.location || '—' }}</span>
                        </li>
                        <li v-if="property.user" class="list-group-item d-flex justify-content-between align-items-start">
                            <span class="text-muted">Listed by</span>
                            <span class="text-end ms-3">
                                {{ property.user.name || '—' }}
                                <span v-if="property.user.email" class="d-block small text-muted">{{
                                    property.user.email
                                }}</span>
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-semibold">Description</div>
                    <div class="card-body">
                        <p v-if="property.description" class="mb-0 text-break" style="white-space: pre-wrap">
                            {{ property.description }}
                        </p>
                        <p v-else class="text-muted mb-0">No description provided.</p>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.thumb-btn {
    width: 72px;
    height: 54px;
    cursor: pointer;
    opacity: 0.85;
    transition: opacity 0.15s ease, box-shadow 0.15s ease;
}

.thumb-btn:hover {
    opacity: 1;
}

.thumb-btn--active {
    opacity: 1;
    box-shadow: 0 0 0 2px var(--bs-primary, #0d6efd);
}

.thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.object-fit-contain {
    object-fit: contain;
}
</style>
