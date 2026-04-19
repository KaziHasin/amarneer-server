<script setup>
import { ref, watch } from 'vue';
import Layout from '@/components/Layout.vue'
import PropertyStatusSelect from '@/components/PropertyStatusSelect.vue'
import { Link, router, usePage } from '@inertiajs/vue3';
import { capitalizeText } from '../../utils/helper';

const page = usePage()
const properties = ref([]);
watch(
    () => page.props.properties,
    (value) => {
        properties.value = Array.isArray(value) ? value : [];
    },
    { immediate: true }
)

const patchProperty = (propertyId, patch) => {
    const idx = properties.value.findIndex((p) => p.id === propertyId);
    if (idx === -1) return;
    properties.value[idx] = { ...properties.value[idx], ...patch };
};
const fields = ref([
    { key: 'name', label: 'Name', sortable: true },
    { key: 'category.name', label: 'Category' },
    { key: 'listing_type', label: 'Type', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'price', label: 'Price', sortable: true },
    { key: 'area', label: 'Area', sortable: true },
    { key: 'location', label: 'Location', sortable: true },
    { key: 'is_featured', label: 'Featured', sortable: true },
    { key: 'actions', label: 'Actions' }
])
const filter = ref('');
const perPage = ref(5);
const currentPage = ref(1);

const getStatusVariant = (status) => {
    if (status === 'approved') return 'success'
    if (status === 'rejected') return 'danger'
    if (status === 'pending') return 'warning'
    return 'secondary'
};

const statusOptions = [
    { value: 'pending', text: 'Pending' },
    { value: 'approved', text: 'Approved' },
    { value: 'rejected', text: 'Rejected' },
];

const updateStatus = (property, nextStatus) => {
    const prev = property.status;
    patchProperty(property.id, { status: nextStatus });

    router.put(
        route('properties.update', property.id),
        { status: nextStatus },
        {
            preserveScroll: true,
            onError: () => {
                patchProperty(property.id, { status: prev });
            },
        }
    );
};

const updateFeatured = (property, nextValue) => {
    const prev = property.is_featured;
    patchProperty(property.id, { is_featured: nextValue });

    router.put(
        route('properties.update', property.id),
        { is_featured: nextValue ? 1 : 0 },
        {
            preserveScroll: true,
            onError: () => {
                patchProperty(property.id, { is_featured: prev });
            },
        }
    );
};
const showAddUserModal = () => {

    form.clearErrors();
    addUserModal.value = true
};
const saveUser = () => {
    form.post(route('properties.store'), {
        onSuccess: () => {
            if (page.props.properties) {
                properties.value = Array.isArray(page.props.properties) ? page.props.properties : [];
            }

            addUserModal.value = false;
            form.reset();
        }
    });
};
const editUser = (user) => {
    console.log('Edit user:', user)
};
const deleteUser = (id) => {
    if (confirm('Are you sure you want to delete this user?')) {
        properties.value = properties.value.filter(user => user.id !== id)
    }
}
</script>

<template>
    <Layout title="properties">
        <div>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>properties</h2>
                <b-button variant="primary" @click="showAddUserModal">
                    <i class="fas fa-plus me-1"></i> Add Properties
                </b-button>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">PropertyList</h5>
                    <div class="d-flex">
                        <b-form-input v-model="filter" type="search" placeholder="Search properties..."
                            class="me-2"></b-form-input>
                        <b-button variant="outline-secondary">
                            <i class="fas fa-filter"></i>
                        </b-button>
                    </div>
                </div>
                <div class="card-body">
                    <b-table striped hover :items="properties" :fields="fields" :filter="filter" :per-page="perPage"
                        :current-page="currentPage" responsive>
                        <template #cell(name)="row">
                            <Link :href="route('properties.show', row.item.id)"
                                class="text-decoration-none text-dark fs-6 text-2xl">
                                {{ row.item.name }}
                            </Link>
                        </template>

                        <template #cell(category.name)="row">
                            <span v-if="row.item.category?.name">{{ row.item.category.name }}</span>
                            <span v-else class="text-muted">—</span>
                        </template>
                        <template #cell(listing_type)="row">
                            <span v-if="row.item.category?.name">{{ capitalizeText(row.item.listing_type) }}</span>
                            <span v-else class="text-muted">—</span>
                        </template>

                        <template #cell(status)="row">
                            <PropertyStatusSelect :property="row.item" :status-options="statusOptions"
                                :get-status-variant="getStatusVariant" :capitalize-text="capitalizeText"
                                @select="(v) => updateStatus(row.item, v)" />
                        </template>

                        <template #cell(is_featured)="row">
                            <b-form-checkbox switch :model-value="!!row.item.is_featured"
                                @update:model-value="(v) => updateFeatured(row.item, v)">
                                <span class="ms-2">{{ row.item.is_featured ? 'Yes' : 'No' }}</span>
                            </b-form-checkbox>
                        </template>

                        <template #cell(actions)="row">
                            <b-button variant="outline-primary" size="sm" @click="editUser(row.item)" class="me-1">
                                <i class="fas fa-edit"></i>
                            </b-button>
                            <b-button variant="outline-danger" size="sm" @click="deleteUser(row.item.id)">
                                <i class="fas fa-trash"></i>
                            </b-button>
                        </template>
                    </b-table>

                    <b-pagination v-model="currentPage" :total-rows="properties.length" :per-page="perPage"
                        class="mt-5 d-flex justify-content-end"></b-pagination>
                </div>
            </div>


        </div>
    </Layout>
</template>

<style scoped>
.text-2xl {
    font-size: 1.2rem !important;
    font-weight: 500;
}
</style>
