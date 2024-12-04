<template>
    <div>
        <a-card title="MASTERFILE"></a-card>
    </div>
        <a-form-item style="margin-top: 20px;">
            <a-card @click="() => $inertia.get(route('admin.masterfile.users'))" class="card-hover"
                style=" background-color: #1e90ff ; color: white; width: 12%; height: auto; position: absolute; top:10px;">
                <UserOutlined /> Setup Users
            </a-card>

            <a-card @click="() => $inertia.get(route('admin.masterfile.store.staff'))" class="card-hover"
                style=" background-color: #1e90ff ; color: white; width: 15%; height: auto;  position: absolute; top:10px; margin-left: 12%;">
                <AppstoreAddOutlined /> Setup Store Staff
            </a-card>

            <a-card @click="() => $inertia.get(route('admin.masterfile.customer.setup'))" class="card-hover"
                style=" background-color: #1e90ff ; color: white; width: 15%; height: auto; position: absolute; top:10px; margin-left: 27%;  ">
                <CustomerServiceOutlined /> Setup Customer
            </a-card>


            <a-card @click="() => $inertia.get(route('admin.masterfile.setupStore'))" class="card-hover"
                style=" background-color: #1e90ff ; color: white; width: 12%; height: auto; position: absolute; top:10px; margin-left: 42%; ">
                <AppstoreFilled /> Setup Store
            </a-card>


            <a-card @click="() => $inertia.get(route('admin.masterfile.creditCardSetup'))" class="card-hover"
                style=" background-color: #1e90ff ; color: white; width: 15%; height: auto; position: absolute; top:10px; margin-left: 54%; ">
                <CreditCardFilled /> Setup Credit Card
            </a-card>



            <a-card @click="() => $inertia.get(route('admin.masterfile.denominationSetup'))" class="card-hover"
                style=" background-color: #1e90ff ; color: white; width: 17%; height: auto; position: absolute; top:10px; margin-left: 69%; ">
                <BarcodeOutlined /> Setup Denomination
            </a-card>



            <a-card @click="() => $inertia.get(route('admin.masterfile.revolvingFund'))" class="card-hover"
                style=" background-color: #1e90ff ; color: white; width: 15%; height: auto; position: absolute; top:10px; margin-left: 86%; ">
                <FundFilled /> Revolving Fund
            </a-card>
        </a-form-item>

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
