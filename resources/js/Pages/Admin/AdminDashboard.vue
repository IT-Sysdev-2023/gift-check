<template>
    <a-row>
        <a-col :span="6">
            <a-card class="bg-slate-300" title="Masterfile">
                <a-form-item>
                    <a-button @click="() => $inertia.get(route('admin.masterfile.users'))" class="mb-2" block><UserOutlined />Setup Users</a-button>
                    <a-button class="mb-2" block><UserOutlined />Setup Store Staff</a-button>
                    <a-button class="mb-2" block><UserOutlined />Setup Customers</a-button>
                    <a-button class="mb-2" block><FileDoneOutlined />Setup Store</a-button>
                    <a-button class="mb-2" block><IdcardOutlined />Setup Credit Card</a-button>
                    <a-button block><BarcodeOutlined />Setup Denomination</a-button>
                </a-form-item>
            </a-card>
        </a-col>
        <a-col :span="6">

        </a-col>
        <a-col :span="6">

        </a-col>
        <a-col :span="6">

        </a-col>
    </a-row>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props:{
        users:Object
    }
}

</script>
