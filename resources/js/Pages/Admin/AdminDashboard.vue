<template>


    <a-card title="Masterfile">
        <a-form-item>
            <div class=" button-container">

                <a-card @click="() => $inertia.get(route('admin.masterfile.users'))" class="mb-5 card-hover"
                    style=" background-color: #0286df; color: white; ">
                    <UserOutlined />  Setup Users
                </a-card>


                <a-card @click="() => $inertia.get(route('admin.masterfile.store.staff'))" class="mb-5 card-hover"
                    style="background-color: #0286df; color: white; margin-left: 5px;">
                    <AppstoreAddOutlined />  Setup Store Staff
                </a-card>

                <a-card @click="() => $inertia.get(route('admin.masterfile.customer.setup'))" class="mb-5 card-hover"
                    style="  background-color: #0286df; color: white; margin-left: 5px;">
                    <CustomerServiceOutlined />  Setup Customers
                </a-card>


                <a-card @click="() => $inertia.get(route('admin.masterfile.setupStore'))" class="mb-5 card-hover"
                    style=" background-color: #0286df; color: white; margin-left: 5px;">
                    <AppstoreFilled />  Setup Store
                </a-card>


                <a-card @click="() => $inertia.get(route('admin.masterfile.creditCardSetup'))" class="mb-5 card-hover"
                    style="  background-color: #0286df; color: white; margin-left: 5px;">
                    <CreditCardFilled />  Setup Credit Card
                </a-card>



                <a-card @click="() => $inertia.get(route('admin.masterfile.denominationSetup'))" class="mb-5 card-hover"
                    style="background-color: #0286df; color: white; margin-left: 5px;">
                    <BarcodeOutlined />  Setup Denomination
                </a-card>



                <a-card @click="() => $inertia.get(route('admin.masterfile.revolvingFund'))" class="mb-5 card-hover"
                    style=" background-color: #0286df; color: white; margin-left: 5px;">
                    <FundFilled />  Revolving Fund
                </a-card>

            </div>
        </a-form-item>
    </a-card>


    <!-- {{ users }} -->
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        users: Object
    }
}

</script>
<style scoped>
.button-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 20px;
}

.button-container .ant-btn {
    margin: 5px;
}
.card-hover {
    transition: transform 0.2s ease;
    cursor: pointer;
}

.card-hover:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}
</style>
