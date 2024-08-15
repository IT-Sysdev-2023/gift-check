<template>
    <a-card class="bg-card">
        <a-alert message="Special Gc Request" show-icon />
        <div class="card" style="background-color: #EE4E4E">
            <svg class="wave" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M0,256L11.4,240C22.9,224,46,192,69,192C91.4,192,114,224,137,234.7C160,245,183,235,206,213.3C228.6,192,251,160,274,149.3C297.1,139,320,149,343,181.3C365.7,213,389,267,411,282.7C434.3,299,457,277,480,250.7C502.9,224,526,192,549,181.3C571.4,171,594,181,617,208C640,235,663,277,686,256C708.6,235,731,149,754,122.7C777.1,96,800,128,823,165.3C845.7,203,869,245,891,224C914.3,203,937,117,960,112C982.9,107,1006,181,1029,197.3C1051.4,213,1074,171,1097,144C1120,117,1143,107,1166,133.3C1188.6,160,1211,224,1234,218.7C1257.1,213,1280,139,1303,133.3C1325.7,128,1349,192,1371,192C1394.3,192,1417,128,1429,96L1440,64L1440,320L1428.6,320C1417.1,320,1394,320,1371,320C1348.6,320,1326,320,1303,320C1280,320,1257,320,1234,320C1211.4,320,1189,320,1166,320C1142.9,320,1120,320,1097,320C1074.3,320,1051,320,1029,320C1005.7,320,983,320,960,320C937.1,320,914,320,891,320C868.6,320,846,320,823,320C800,320,777,320,754,320C731.4,320,709,320,686,320C662.9,320,640,320,617,320C594.3,320,571,320,549,320C525.7,320,503,320,480,320C457.1,320,434,320,411,320C388.6,320,366,320,343,320C320,320,297,320,274,320C251.4,320,229,320,206,320C182.9,320,160,320,137,320C114.3,320,91,320,69,320C45.7,320,23,320,11,320L0,320Z"
                    fill-opacity="1"></path>
            </svg>

            <div class="icon-container">
                <ExclamationCircleFilled />
            </div>

            <div class="message-text-container">
                <p class="message-text text-white">Pendings</p>
                <p class="sub-text text-white">All Pending Request</p>
            </div>
            <a-badge >
                <a-button >
                    <template #icon>
                        <FolderFilled />
                    </template>
                    View
                </a-button>
            </a-badge>

        </div>

        <div class="card" style="background-color: #34B3F1">
            <svg class="wave" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M0,256L11.4,240C22.9,224,46,192,69,192C91.4,192,114,224,137,234.7C160,245,183,235,206,213.3C228.6,192,251,160,274,149.3C297.1,139,320,149,343,181.3C365.7,213,389,267,411,282.7C434.3,299,457,277,480,250.7C502.9,224,526,192,549,181.3C571.4,171,594,181,617,208C640,235,663,277,686,256C708.6,235,731,149,754,122.7C777.1,96,800,128,823,165.3C845.7,203,869,245,891,224C914.3,203,937,117,960,112C982.9,107,1006,181,1029,197.3C1051.4,213,1074,171,1097,144C1120,117,1143,107,1166,133.3C1188.6,160,1211,224,1234,218.7C1257.1,213,1280,139,1303,133.3C1325.7,128,1349,192,1371,192C1394.3,192,1417,128,1429,96L1440,64L1440,320L1428.6,320C1417.1,320,1394,320,1371,320C1348.6,320,1326,320,1303,320C1280,320,1257,320,1234,320C1211.4,320,1189,320,1166,320C1142.9,320,1120,320,1097,320C1074.3,320,1051,320,1029,320C1005.7,320,983,320,960,320C937.1,320,914,320,891,320C868.6,320,846,320,823,320C800,320,777,320,754,320C731.4,320,709,320,686,320C662.9,320,640,320,617,320C594.3,320,571,320,549,320C525.7,320,503,320,480,320C457.1,320,434,320,411,320C388.6,320,366,320,343,320C320,320,297,320,274,320C251.4,320,229,320,206,320C182.9,320,160,320,137,320C114.3,320,91,320,69,320C45.7,320,23,320,11,320L0,320Z"
                    fill-opacity="1"></path>
            </svg>

            <div class="icon-container">
                <CheckCircleFilled />
            </div>
            <div class="message-text-container">
                <p class="message-text text-white">Approved</p>
                <p class="sub-text text-white">Approved Gc Request</p>
            </div>
            <a-badge >
                <a-button>
                    <template #icon>
                        <FolderFilled />
                    </template>
                    View
                </a-button>
            </a-badge>

        </div>

        <div class="card" style="background-color: #758694">
            <svg class="wave" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M0,256L11.4,240C22.9,224,46,192,69,192C91.4,192,114,224,137,234.7C160,245,183,235,206,213.3C228.6,192,251,160,274,149.3C297.1,139,320,149,343,181.3C365.7,213,389,267,411,282.7C434.3,299,457,277,480,250.7C502.9,224,526,192,549,181.3C571.4,171,594,181,617,208C640,235,663,277,686,256C708.6,235,731,149,754,122.7C777.1,96,800,128,823,165.3C845.7,203,869,245,891,224C914.3,203,937,117,960,112C982.9,107,1006,181,1029,197.3C1051.4,213,1074,171,1097,144C1120,117,1143,107,1166,133.3C1188.6,160,1211,224,1234,218.7C1257.1,213,1280,139,1303,133.3C1325.7,128,1349,192,1371,192C1394.3,192,1417,128,1429,96L1440,64L1440,320L1428.6,320C1417.1,320,1394,320,1371,320C1348.6,320,1326,320,1303,320C1280,320,1257,320,1234,320C1211.4,320,1189,320,1166,320C1142.9,320,1120,320,1097,320C1074.3,320,1051,320,1029,320C1005.7,320,983,320,960,320C937.1,320,914,320,891,320C868.6,320,846,320,823,320C800,320,777,320,754,320C731.4,320,709,320,686,320C662.9,320,640,320,617,320C594.3,320,571,320,549,320C525.7,320,503,320,480,320C457.1,320,434,320,411,320C388.6,320,366,320,343,320C320,320,297,320,274,320C251.4,320,229,320,206,320C182.9,320,160,320,137,320C114.3,320,91,320,69,320C45.7,320,23,320,11,320L0,320Z"
                    fill-opacity="1"></path>
            </svg>

            <div class="icon-container">
                <CloseCircleFilled />
            </div>
            <div class="message-text-container">
                <p class="message-text text-white">Cancelled</p>
                <p class="sub-text text-white">All Cancel Request</p>
            </div>
            <a-button>
                <template #icon>
                    <FolderFilled />
                </template>
                View
            </a-button>
        </div>
    </a-card>
</template>
<script>
export default {
    props: {
        count: Array,
    }
}
</script>
<style scoped>
.bg-card {
    box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
}

.card {

    height: 65px;
    border-radius: 8px;
    margin-top: 7px;
    box-sizing: border-box;
    padding: 10px 15px;
    /* background-color: #ffffff; */
    box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-around;
    gap: 15px;
}

.wave {
    position: absolute;
    transform: rotate(90deg);
    left: -31px;
    top: 32px;
    width: 80px;
    fill: #fff;
}

.icon-container {
    width: 35px;
    height: 35px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: white;
    border-radius: 50%;
    margin-left: 8px;
}

.icon {
    width: 17px;
    height: 17px;
    color: #269b24;
}

.message-text-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    flex-grow: 1;
}

.message-text,
.sub-text {
    margin: 0;
    cursor: default;
}

.message-text {
    font-size: 17px;
    font-weight: 700;
}

.sub-text {
    font-size: 14px;
}

.cross-icon {
    width: 18px;
    height: 18px;
    color: #555;
    cursor: pointer;
}
</style>
