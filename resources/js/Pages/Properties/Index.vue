<script setup>
import { ref, watch } from 'vue';
import Layout from '@/components/Layout.vue'
import { usePage } from '@inertiajs/vue3';

const page = usePage()
const properties = ref([]);
watch(
    () => page.props.properties,
    (value) => {
        properties.value = Array.isArray(value) ? value : [];
    },
    { immediate: true }
)
const fields = ref([
    { key: 'name', label: 'Name', sortable: true },
    { key: 'category.name', label: '' },
    { key: 'listing_type', label: 'Type', sortable: true },
    { key: 'type', label: 'Role', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
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
                    <i class="fas fa-plus me-1"></i> Add User
                </b-button>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">User List</h5>
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
                        <template #cell(status)="data">
                            <b-badge :variant="getStatusVariant(data.value)">
                                {{ data.value }}
                            </b-badge>
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
