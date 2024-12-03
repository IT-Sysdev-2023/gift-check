<template>

    <a-card title="Masterfile">
        <a-form-item>
            <div>
                <a-card @click="() => $inertia.get(route('admin.masterfile.users'))" class="card-hover"
                    style=" background-color: #3498db ; color: white; width: 18%; height: auto;">
                    <UserOutlined /> SETUP USERS
                </a-card>

                <a-card @click="() => $inertia.get(route('admin.masterfile.store.staff'))" class="card-hover"
                    style=" background-color: #3498db ; color: white; width: 18%; height: auto; margin-top: 10px;">
                    <AppstoreAddOutlined /> SETUP STORE STAFF
                </a-card>

                <a-card @click="() => $inertia.get(route('admin.masterfile.customer.setup'))" class="card-hover"
                    style=" background-color: #3498db ; color: white; width: 18%; height: auto; margin-top: 10px;">
                    <CustomerServiceOutlined /> SETUP CUSTOMER
                </a-card>


                <a-card @click="() => $inertia.get(route('admin.masterfile.setupStore'))" class="card-hover"
                    style=" background-color: #3498db ; color: white; width: 18%; height: auto; margin-top: 10px;">
                    <AppstoreFilled /> SETUP STORE
                </a-card>


                <a-card @click="() => $inertia.get(route('admin.masterfile.creditCardSetup'))" class="card-hover"
                    style=" background-color: #3498db ; color: white; width: 18%; height: auto; margin-top: 10px;">
                    <CreditCardFilled /> SETUP CREDIT CARD
                </a-card>



                <a-card @click="() => $inertia.get(route('admin.masterfile.denominationSetup'))" class="card-hover"
                    style=" background-color: #3498db ; color: white; width: 18%; height: auto; margin-top: 10px;">
                    <BarcodeOutlined /> SETUP DENOMINATION
                </a-card>



                <a-card @click="() => $inertia.get(route('admin.masterfile.revolvingFund'))" class="card-hover"
                    style=" background-color: #3498db ; color: white; width: 18%; height: auto; margin-top: 10px;">
                    <FundFilled /> REVOLVING FUND
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
