<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { UserType } from '@/userType';

const {userType, userRole} = UserType();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- hwllo -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in!</div>
                    <h1 v-if="userType('7') "> This is Hidden</h1>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
