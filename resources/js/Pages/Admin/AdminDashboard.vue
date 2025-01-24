<template>
    <div>
        <h2>Masterfile</h2>
    </div>
    <div
        style="display: flex; margin-top: 2rem; justify-content: center; align-items: center; flex-direction: column; ">
        <a-form-item>
            <a-button @click="() => $inertia.get(route('admin.masterfile.users'))" class="buttons">
                <UserOutlined /> Setup Users
            </a-button>

            <a-button @click="() => $inertia.get(route('admin.masterfile.store.staff'))" class="buttons">
                <AppstoreAddOutlined /> Setup Store Staff
            </a-button>

            <a-button @click="() => $inertia.get(route('admin.masterfile.customer.setup'))" class="buttons">
                <CustomerServiceOutlined /> Setup Customer
            </a-button>

            <a-button @click="() => $inertia.get(route('admin.masterfile.setupStore'))" class="buttons">
                <AppstoreFilled /> Setup Store
            </a-button>


            <a-button @click="() => $inertia.get(route('admin.masterfile.creditCardSetup'))" class="buttons">
                <CreditCardFilled /> Setup Credit Card
            </a-button>

            <a-button @click="() => $inertia.get(route('admin.masterfile.denominationSetup'))" class="buttons">
                <BarcodeOutlined /> Setup Denomination
            </a-button>

            <a-button @click="() => $inertia.get(route('admin.masterfile.revolvingFund'))" class="buttons">
                <FundFilled /> Revolving Fund
            </a-button>

            <a-button @click="() => $inertia.get(route('admin.masterfile.tagHennan'))" class="buttons">
                <TagOutlined /> Tag Hennan
            </a-button>

            <a-button @click="() => $inertia.get(route('admin.masterfile.blockBarcode'))" class="buttons"
                style="margin-top: 1rem;">
                <StopOutlined /> Blocked Barcode
            </a-button>
        </a-form-item>

    </div>

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
    justify-content: center;
    align-items: center;
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

.buttons {
    background-color: #113961;
    height: 5rem;
    color: white;
    transition: transform 0.2s ease;
    cursor: pointer;
    margin: 0 5px;
}

.buttons:hover {
    background-color: #1b76f8;
    color: white;
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}
</style>
