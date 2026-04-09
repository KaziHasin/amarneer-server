<template>
    <Layout title="Dashboard">
        <h2 class="mb-4">Dashboard</h2>

        <DashboardStats />

        <div class="row mt-4">
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Sales Analytics</h5>
                        <div>
                            <b-button variant="outline-primary" size="sm">Week</b-button>
                            <b-button variant="outline-primary" size="sm" class="ms-2">Month</b-button>
                            <b-button variant="primary" size="sm" class="ms-2">Year</b-button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart would go here -->
                        <div class="chart-placeholder" style="height: 300px; background-color: #f8f9fa;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="activity-list">
                            <div class="activity-item d-flex mb-3" v-for="(item, index) in activities" :key="index">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm">
                                        <span
                                            class="avatar-title rounded-circle bg-primary text-white d-inline-block text-center"
                                            style="width: 25px; height:25px;">
                                            {{ item.user.charAt(0) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ item.user }}</h6>
                                    <p class="mb-0 text-muted">{{ item.action }}</p>
                                    <small class="text-muted">{{ item.time }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Top Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <b-table striped hover :items="products" :fields="productFields"></b-table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <b-table striped hover :items="orders" :fields="orderFields"></b-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>

import { ref } from 'vue'
import DashboardStats from '@/components/DashboardStats.vue';
import Layout from '@/components/Layout.vue'

const activities = ref([
    { user: 'John Doe', action: 'Created a new order #1234', time: '5 mins ago' },
    { user: 'Jane Smith', action: 'Updated product details', time: '15 mins ago' },
    { user: 'Robert Johnson', action: 'Deleted a customer account', time: '30 mins ago' },
    { user: 'Emily Davis', action: 'Processed 5 new orders', time: '1 hour ago' }
]);
const products = ref([
    { id: 1, name: 'Premium Headphones', price: '$199', stock: 45, sales: 125 },
    { id: 2, name: 'Wireless Keyboard', price: '$89', stock: 78, sales: 98 },
    { id: 3, name: 'Smart Watch', price: '$249', stock: 32, sales: 87 },
    { id: 4, name: 'Bluetooth Speaker', price: '$129', stock: 56, sales: 76 }
]);
const productFields = ref([
    { key: 'name', label: 'Product' },
    { key: 'price', label: 'Price' },
    { key: 'stock', label: 'Stock' },
    { key: 'sales', label: 'Sales' }
]);
const orders = ref([
    { id: '#1234', customer: 'John Doe', date: '2023-05-15', amount: '$199', status: 'Completed' },
    { id: '#1235', customer: 'Jane Smith', date: '2023-05-14', amount: '$89', status: 'Processing' },
    { id: '#1236', customer: 'Robert Johnson', date: '2023-05-14', amount: '$249', status: 'Shipped' },
    { id: '#1237', customer: 'Emily Davis', date: '2023-05-13', amount: '$129', status: 'Pending' }
]);
const orderFields = ref([
    { key: 'id', label: 'Order ID' },
    { key: 'customer', label: 'Customer' },
    { key: 'date', label: 'Date' },
    { key: 'amount', label: 'Amount' },
    { key: 'status', label: 'Status' }
]);
</script>