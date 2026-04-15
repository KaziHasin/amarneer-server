<script setup>
import { ref, watch } from 'vue';
import Layout from '@/components/Layout.vue'
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage()
const users = ref([]);
watch(
    () => page.props.users,
    (value) => {
        users.value = Array.isArray(value) ? value : [];
    },
    { immediate: true }
)
const fields = ref([
    { key: 'avatar', label: '' },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'type', label: 'Role', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'actions', label: 'Actions' }
])
const filter = ref('');
const perPage = ref(5);
const currentPage = ref(1);
const addUserModal = ref(false);
const form = useForm({
    name: '',
    email: '',
    type: null,
});
const roleOptions = ref([
    { value: null, text: 'Select Role' },
    { value: 'admin', text: 'Admin' },
    { value: 'customer', text: 'Customer' },
    { value: 'owner', text: 'Owner' }
])


const getStatusVariant = (status) => {
    const value = String(status ?? '').toLowerCase()
    if (value === 'active') return 'success'
    if (value === 'inactive') return 'danger'
    if (value === 'pending') return 'warning'
    return 'secondary'
};
const showAddUserModal = () => {

    form.clearErrors();
    addUserModal.value = true
};
const saveUser = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            if (page.props.users) {
                users.value = Array.isArray(page.props.users) ? page.props.users : [];
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
        users.value = users.value.filter(user => user.id !== id)
    }
}
</script>

<template>
    <Layout title="Users">
        <div>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Users</h2>
                <b-button variant="primary" @click="showAddUserModal">
                    <i class="fas fa-plus me-1"></i> Add User
                </b-button>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">User List</h5>
                    <div class="d-flex">
                        <b-form-input v-model="filter" type="search" placeholder="Search users..."
                            class="me-2"></b-form-input>
                        <b-button variant="outline-secondary">
                            <i class="fas fa-filter"></i>
                        </b-button>
                    </div>
                </div>
                <div class="card-body">
                    <b-table striped hover :items="users" :fields="fields" :filter="filter" :per-page="perPage"
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

                    <b-pagination v-model="currentPage" :total-rows="users.length" :per-page="perPage"
                        class="mt-5 d-flex justify-content-end"></b-pagination>
                </div>
            </div>

            <!-- Add User Modal -->
            <b-modal v-model="addUserModal" title="Add New User" hide-footer>
                <b-form @submit.prevent="saveUser">
                    <b-form-group label="Full Name" label-for="name">
                        <b-form-input
                            id="name"
                            v-model="form.name"
                            :state="form.errors.name ? false : null"
                            required
                        ></b-form-input>
                        <b-form-invalid-feedback v-if="form.errors.name">
                            {{ form.errors.name }}
                        </b-form-invalid-feedback>
                    </b-form-group>

                    <b-form-group label="Email" label-for="email">
                        <b-form-input
                            id="email"
                            v-model="form.email"
                            type="email"
                            :state="form.errors.email ? false : null"
                            required
                        ></b-form-input>
                        <b-form-invalid-feedback v-if="form.errors.email">
                            {{ form.errors.email }}
                        </b-form-invalid-feedback>
                    </b-form-group>

                    <b-form-group label="Role" label-for="role">
                        <b-form-select
                            id="role"
                            v-model="form.type"
                            :options="roleOptions"
                            :state="form.errors.type ? false : null"
                            required
                        ></b-form-select>
                        <b-form-invalid-feedback v-if="form.errors.type">
                            {{ form.errors.type }}
                        </b-form-invalid-feedback>
                    </b-form-group>



                    <div class="d-flex justify-content-end mt-4">
                        <b-button variant="secondary" @click="addUserModal = false" class="me-2">Cancel</b-button>
                        <b-button variant="primary" type="submit">Save User</b-button>
                    </div>
                </b-form>
            </b-modal>
        </div>
    </Layout>
</template>
