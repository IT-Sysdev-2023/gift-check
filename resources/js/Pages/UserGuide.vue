<template>
    <header style="
            background-color: #fbf8ef;
            color: black;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        ">
        <!-- GIFT CHECK ICON  -->
        <div>
            <a>
                <img src="/gcicon.png" style="
                        border-radius: 10%;
                        border: 5px solid white;
                        width: 120px;
                        height: 120px;
                        display: inline;
                        justify-content: center;
                        align-items: center;
                    " />
            </a>
        </div>

        <!-- HOME ICON BUTTON  -->
        <div>
            <a @click="() => {
                gcRequisitionPerStorePerPage = false;
                howToMakeGCProductionPerPage = false;
                howToRequestPromoPerPage = false;
                howToRequestSpecialExternalPerPage = false;
                treasuryDepartmentPerPage = false;
                homePage = true;
                pdfGcProductionRegularGc = false;
                pdfhowToMakeGCProductionPerPage = false;
                pdfHowToRequestPromo = false;
                pdfHowToRequestSpecialExternal = false;
                pdfTreasuryDepartment = false;
            }
                " style="cursor: pointer">
                <div class="menu">
                    <a href="#" class="link">
                        <span class="link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="192" height="192" fill="currentColor"
                                viewBox="0 0 256 256">
                                <rect width="256" height="256" fill="none"></rect>
                                <path
                                    d="M213.3815,109.61945,133.376,36.88436a8,8,0,0,0-10.76339.00036l-79.9945,72.73477A8,8,0,0,0,40,115.53855V208a8,8,0,0,0,8,8H208a8,8,0,0,0,8-8V115.53887A8,8,0,0,0,213.3815,109.61945Z"
                                    fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="16"></path>
                            </svg>
                        </span>
                        <span class="link-title">Home</span>
                    </a>
                </div>
            </a>
        </div>

        <!-- NAVIGATION BUTTONS  -->
        <nav>
            <ul style="
                    list-style-type: none;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    gap: 150px;
                ">
                <!-- USER GUIDE PER PAGE  -->
                <a-dropdown>
                    <a class="ant-dropdown-link" @click.prevent style="
                            font-family: sans-serif;
                            font-weight: bold;
                            font-size: 17px;
                            color: #003140;
                        ">View UserGuide by Page
                        <DownOutlined />
                    </a>
                    <template #overlay>
                        <a-menu>
                            <a-menu-item>
                                <a @click="() => {
                                    gcRequisitionPerStorePerPage = true;
                                    howToMakeGCProductionPerPage;
                                    pdfhowToMakeGCProductionPerPage = false;
                                    howToRequestPromoPerPage = false;
                                    howToRequestSpecialExternalPerPage = false;
                                    treasuryDepartmentPerPage = false;
                                    homePage = false;
                                    pdfGcProductionRegularGc = false;
                                    pdfHowToRequestPromo = false;
                                    pdfHowToRequestSpecialExternal = false;
                                    pdfTreasuryDepartment = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    GC Requisition Per Store</a>
                            </a-menu-item>
                            <a-menu-item>
                                <a @click="() => {
                                    howToMakeGCProductionPerPage = true;
                                    gcRequisitionPerStorePerPage = false;
                                    howToRequestPromoPerPage = f;
                                    pdfhowToMakeGCProductionPerPage = false;
                                    howToRequestSpecialExternalPerPage = false;
                                    treasuryDepartmentPerPage = false;
                                    homePage = false;
                                    pdfGcProductionRegularGc = false;
                                    pdfHowToRequestPromo = false;
                                    pdfHowToRequestSpecialExternal = false;
                                    pdfTreasuryDepartment = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    How to Make GC Production (Regular GC)</a>
                            </a-menu-item>
                            <a-menu-item>
                                <a @click="() => {
                                    howToRequestPromoPerPage = true;
                                    howToMakeGCProductionPerPage = false;
                                    gcRequisitionPerStorePerPage = false;
                                    howToRequestSpecialExternalP;
                                    pdfhowToMakeGCProductionPerPage = false;
                                    treasuryDepartmentPerPage = false;
                                    homePage = false;
                                    pdfGcProductionRegularGc = false;
                                    pdfHowToRequestPromo = false;
                                    pdfHowToRequestSpecialExternal = false;
                                    pdfTreasuryDepartment = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    How to Request Promo</a>
                            </a-menu-item>
                            <a-menu-item>
                                <a @click="() => {
                                    howToRequestSpecialExternalPerPage = true;
                                    howToRequestPromoPerPage = false;
                                    howToMakeGCProductionPerPage = false;
                                    gcRequisitionPerStorePerPage = false;
                                    treasuryDepartmentPerPage =
                                        pdfhowToMakeGCProductionPerPage = false;
                                    homePage = false;
                                    pdfGcProductionRegularGc = false;
                                    pdfHowToRequestPromo = false;
                                    pdfHowToRequestSpecialExternal = false;
                                    pdfTreasuryDepartment = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    How to Request Special External GC</a>
                            </a-menu-item>
                            <a-menu-item>
                                <a @click="() => {
                                    treasuryDepartmentPerPage = true;
                                    howToRequestSpecialExternalPerPage = false;
                                    howToRequestPromoPerPage = false;
                                    howToMakeGCProductionPerPage = false;
                                    gcRequisitionPerStorePerPage = false;
                                    homePage = false;
                                    pdfhowToMakeGCProductionPerPage = false;
                                    pdfGcProductionRegularGc = false;
                                    pdfHowToRequestPromo = false;
                                    pdfHowToRequestSpecialExternal = false;
                                    pdfTreasuryDepartment = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    Treasury Department</a>
                            </a-menu-item>
                            <a-menu-item>
                                <a @click="() => {
                                    treasuryDepartmentPerPage = true;
                                    howToRequestSpecialExternalPerPage = true;
                                    howToRequestPromoPerPage = true;
                                    howToMakeGCProductionPerPage = true;
                                    gcRequisitionPerStorePerPage = true;
                                    homePage = false;
                                    pdfhowToMakeGCProductionPerPage = false;
                                    pdfGcProductionRegularGc = false;
                                    pdfHowToRequestPromo = false;
                                    pdfHowToRequestSpecialExternal = false;
                                    pdfTreasuryDepartment = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    <EyeOutlined /> Show All
                                </a>
                            </a-menu-item>
                        </a-menu>
                    </template>
                </a-dropdown>

                <!-- USER GUIDE IN PDF  -->
                <a-dropdown>
                    <a style="
                            font-family: sans-serif;
                            font-weight: bold;
                            color: #003140;
                            font-size: 17px;
                        " class="ant-dropdown-link" @click.prevent>View UserGuide by PDF
                        <DownOutlined />
                    </a>
                    <template #overlay>
                        <a-menu>
                            <a-menu-item>
                                <a @click="() => {
                                    pdfGcProductionRegularGc = true;
                                    gcRequisitionPerStorePerPage = false;
                                    howToMakeGCProductionPerPage = false;
                                    howToRequestPromoPerPage = false;
                                    howToRequestSpecialExternalPerPage = false;
                                    treasuryDepartmentPerPage = false;
                                    pdfhowToMakeGCProductionPerPage = false;
                                    pdfHowToRequestPromo = false;
                                    pdfHowToRequestSpecialExternal = false;
                                    pdfTreasuryDepartment = false;
                                    homePage = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    GC Requisition Per Store</a>
                            </a-menu-item>
                            <a-menu-item>
                                <a @click="() => {
                                    pdfhowToMakeGCProductionPerPage = true;
                                    pdfGcProductionRegularGc = false;
                                    gcRequisitionPerStorePerPage = false;
                                    howToMakeGCProductionPerPage = false;
                                    howToRequestPromoPerPage = false;
                                    howToRequestSpecialExternalPerPage = false;
                                    treasuryDepartmentPerPage = false;
                                    pdfHowToRequestPromo = false;
                                    pdfHowToRequestSpecialExternal = false;
                                    pdfTreasuryDepartment = false;
                                    homePage = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    How to Make GC Production (Regular GC)</a>
                            </a-menu-item>
                            <a-menu-item>
                                <a @click="() => {
                                    pdfHowToRequestPromo = true;
                                    pdfhowToMakeGCProductionPerPage = false;
                                    pdfGcProductionRegularGc = false;
                                    gcRequisitionPerStorePerPage = false;
                                    howToMakeGCProductionPerPage = false;
                                    howToRequestPromoPerPage = false;
                                    howToRequestSpecialExternalPerPage = false;
                                    treasuryDepartmentPerPage = false;
                                    pdfHowToRequestSpecialExternal = false;
                                    pdfTreasuryDepartment = false;
                                    homePage = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    How to Request Promo</a>
                            </a-menu-item>
                            <a-menu-item>
                                <a @click="() => {
                                    pdfHowToRequestSpecialExternal = true;
                                    pdfHowToRequestPromo = false;
                                    pdfhowToMakeGCProductionPerPage = false;
                                    pdfGcProductionRegularGc = false;
                                    gcRequisitionPerStorePerPage = false;
                                    howToMakeGCProductionPerPage = false;
                                    howToRequestPromoPerPage = false;
                                    howToRequestSpecialExternalPerPage = false;
                                    treasuryDepartmentPerPage = false;
                                    pdfTreasuryDepartment = false;
                                    homePage = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    How to Request Special External GC</a>
                            </a-menu-item>
                            <a-menu-item>
                                <a @click="() => {
                                    pdfTreasuryDepartment = true;
                                    pdfHowToRequestSpecialExternal = false;
                                    pdfHowToRequestPromo = false;
                                    pdfhowToMakeGCProductionPerPage = false;
                                    pdfGcProductionRegularGc = false;
                                    gcRequisitionPerStorePerPage = false;
                                    howToMakeGCProductionPerPage = false;
                                    howToRequestPromoPerPage = false;
                                    howToRequestSpecialExternalPerPage = false;
                                    treasuryDepartmentPerPage = false;
                                    homePage = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    Treasury Department</a>
                            </a-menu-item>
                            <a-menu-item>
                                <a @click="() => {
                                    pdfTreasuryDepartment = true;
                                    pdfHowToRequestSpecialExternal = true;
                                    pdfHowToRequestPromo = true;
                                    pdfhowToMakeGCProductionPerPage = true;
                                    pdfGcProductionRegularGc = true;
                                    gcRequisitionPerStorePerPage = false;
                                    howToMakeGCProductionPerPage = false;
                                    howToRequestPromoPerPage = false;
                                    howToRequestSpecialExternalPerPage = false;
                                    treasuryDepartmentPerPage = false;
                                    homePage = false;
                                }
                                    " class="heading-button" href="javascript:;">
                                    <EyeOutlined /> Show All
                                </a>
                            </a-menu-item>
                        </a-menu>
                    </template>
                </a-dropdown>
            </ul>
        </nav>

        <!-- EXIT BUTTON  -->
        <div>
            <button onclick="location.href='http://127.0.0.1/';" class="animated-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="arr-2" viewBox="0 0 24 24">
                    <path
                        d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z">
                    </path>
                </svg>
                <span class="text">E X I T</span>
                <span class="circle"></span>
                <svg xmlns="http://www.w3.org/2000/svg" class="arr-1" viewBox="0 0 24 24">
                    <path
                        d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z">
                    </path>
                </svg>
            </button>
        </div>
    </header>

    <!-- SCROLL UP BUTTON  -->
    <div>
        <button class="button" @click="scrollUp" v-if="showScrollButton">
            <svg class="svgIcon" viewBox="0 0 384 512">
                <path
                    d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z">
                </path>
            </svg>
        </button>
    </div>

    <!-- Home Page  -->
    <a-card v-if="homePage" style="text-align: center"><span style="
                font-size: 40px;
                color: #276047;
                font-weight: bold;
                font-family: sans-serif;
            ">Welcome to</span>
        <span style="font-size: 80px" class="gift-check-colors">
            GIFT CHECK
        </span>
        <span style="
                font-size: 30px;
                font-family: sans-serif;

                font-weight: bold;
                color: #003140;
            ">
            User Guide
        </span>
        <div>
            <a>
                <img src="https://user-images.githubusercontent.com/74038190/221352975-94759904-aa4c-4032-a8ab-b546efb9c478.gif"
                    style="width: 1100px; height: 500px" />
            </a>
        </div>
    </a-card>

    <!-- CARDS CONTAIN THE USERS GUIDE -->

    <!-- GC Requisition Per Store  -->
    <a-card v-if="gcRequisitionPerStorePerPage" style="text-align: center">
        <div style="
                font-size: 40px;
                color: #eb5b00;
                font-weight: bold;
                font-family: sans-serif;
            ">
            GC Requisition Per Store
        </div>
        <div>
            <ul>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 1
                    </div>
                    <img src="/images/userGuidePics/gcRequisitionPerStore/1.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 2
                    </div>

                    <img src="/images/userGuidePics/gcRequisitionPerStore/2.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 3
                    </div>

                    <img src="/images/userGuidePics/gcRequisitionPerStore/3.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 4
                    </div>

                    <img src="/images/userGuidePics/gcRequisitionPerStore/4.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 5
                    </div>

                    <img src="/images/userGuidePics/gcRequisitionPerStore/5.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 6
                    </div>

                    <img src="/images/userGuidePics/gcRequisitionPerStore/6.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 7
                    </div>

                    <img src="/images/userGuidePics/gcRequisitionPerStore/7.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 8
                    </div>

                    <img src="/images/userGuidePics/gcRequisitionPerStore/8.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 9
                    </div>

                    <img src="/images/userGuidePics/gcRequisitionPerStore/9.jpg" style="width: 80%; height: 1600px" />
                </a>
            </ul>
        </div>
    </a-card>

    <!-- VIEW PER PAGE CARD -->

    <!-- How to Make GC Production (Regular GC)  -->
    <a-card v-if="howToMakeGCProductionPerPage" style="text-align: center">
        <div style="
                font-size: 40px;
                color: #eb5b00;
                font-weight: bold;
                font-family: sans-serif;
            ">
            How to Make GC Production (Regular GC)
        </div>
        <div>
            <ul>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 1
                    </div>
                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/1.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 2
                    </div>

                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/2.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 3
                    </div>

                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/3.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 4
                    </div>

                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/4.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 5
                    </div>

                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/5.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 6
                    </div>

                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/6.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 7
                    </div>

                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/7.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 8
                    </div>

                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/8.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 9
                    </div>

                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/9.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 10
                    </div>

                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/10.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 11
                    </div>

                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/11.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <div style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 12
                    </div>

                    <img src="/images/userGuidePics/howToMakeGcProduction(RegularGc)/12.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
            </ul>
        </div>
    </a-card>

    <!-- How to Request Promo  -->
    <a-card v-if="howToRequestPromoPerPage" style="text-align: center">
        <h2 style="
                font-size: 40px;
                color: #eb5b00;
                font-weight: bold;
                font-family: sans-serif;
            ">
            How to Request Promo
        </h2>
        <div>
            <ul>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 1
                    </h2>
                    <img src="/images/userGuidePics/howToRequestPromo/1.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 2
                    </h2>

                    <img src="/images/userGuidePics/howToRequestPromo/2.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 3
                    </h2>

                    <img src="/images/userGuidePics/howToRequestPromo/3.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 4
                    </h2>

                    <img src="/images/userGuidePics/howToRequestPromo/4.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 5
                    </h2>

                    <img src="/images/userGuidePics/howToRequestPromo/5.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 6
                    </h2>

                    <img src="/images/userGuidePics/howToRequestPromo/6.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 7
                    </h2>

                    <img src="/images/userGuidePics/howToRequestPromo/7.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 8
                    </h2>

                    <img src="/images/userGuidePics/howToRequestPromo/8.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 9
                    </h2>

                    <img src="/images/userGuidePics/howToRequestPromo/9.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 10
                    </h2>

                    <img src="/images/userGuidePics/howToRequestPromo/10.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 11
                    </h2>

                    <img src="/images/userGuidePics/howToRequestPromo/11.jpg" style="width: 80%; height: 1600px" />
                </a>
            </ul>
        </div>
    </a-card>

    <!-- How to Request Special External GC  -->
    <a-card v-if="howToRequestSpecialExternalPerPage" style="text-align: center">
        <h2 style="
                font-size: 40px;
                color: #eb5b00;
                font-weight: bold;
                font-family: sans-serif;
            ">
            How to Request Special External GC
        </h2>
        <div>
            <ul>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;
                            margin-top: 100px;
                        ">
                        PAGE 1
                    </h2>
                    <img src="/images/userGuidePics/howToRequestSpecialExternalGc/1.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 2
                    </h2>

                    <img src="/images/userGuidePics/howToRequestSpecialExternalGc/2.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 3
                    </h2>

                    <img src="/images/userGuidePics/howToRequestSpecialExternalGc/3.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 4
                    </h2>

                    <img src="/images/userGuidePics/howToRequestSpecialExternalGc/4.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 5
                    </h2>

                    <img src="/images/userGuidePics/howToRequestSpecialExternalGc/5.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 6
                    </h2>

                    <img src="/images/userGuidePics/howToRequestSpecialExternalGc/6.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 7
                    </h2>

                    <img src="/images/userGuidePics/howToRequestSpecialExternalGc/7.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 8
                    </h2>

                    <img src="/images/userGuidePics/howToRequestSpecialExternalGc/8.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 9
                    </h2>

                    <img src="/images/userGuidePics/howToRequestSpecialExternalGc/9.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 10
                    </h2>

                    <img src="/images/userGuidePics/howToRequestSpecialExternalGc/10.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 11
                    </h2>

                    <img src="/images/userGuidePics/howToRequestSpecialExternalGc/11.jpg"
                        style="width: 80%; height: 1600px" />
                </a>
            </ul>
        </div>
    </a-card>

    <!-- Treasury Department  -->
    <a-card v-if="treasuryDepartmentPerPage" style="text-align: center">
        <h2 style="
                font-size: 40px;
                color: #eb5b00;
                font-weight: bold;
                font-family: sans-serif;
            ">
            Treasury Department
        </h2>
        <div>
            <ul>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 1
                    </h2>
                    <img src="/images/userGuidePics/treasuryDepartment/1.jpg" style="width: 80%; height: 1600px" />
                </a>
                <a>
                    <h2 style="
                            font-weight: bold;
                            color: black;
                            font-family: sans-serif;

                            margin-top: 100px;
                        ">
                        PAGE 2
                    </h2>

                    <img src="/images/userGuidePics/treasuryDepartment/2.jpg" style="width: 80%; height: 1600px" />
                </a>
            </ul>
        </div>
    </a-card>

    <!-- VIEW PER PDF CARD -->

    <!-- Gc Requisition Per Store PDF  -->
    <a-card v-if="pdfGcProductionRegularGc" style="text-align: center"><span style="
                font-size: 40px;
                color: #eb5b00;
                font-weight: bold;
                font-family: sans-serif;
            ">
            GC Requisition Per Store PDF
        </span>
        <div>
            <iframe src="/images/userGuidePics/pdfUserGuide/GC REQUISITION PER STORE.pdf"
                style="width: 100%; height: 1000px; border: none">
            </iframe>
        </div>
    </a-card>

    <!-- How to Make GC Production (Regular GC) PDF  -->
    <a-card v-if="pdfhowToMakeGCProductionPerPage" style="text-align: center">
        <span style="
                font-size: 40px;
                color: #eb5b00;
                font-weight: bold;
                font-family: sans-serif;
            ">
            How to Make GC Production (Regular GC) PDF
        </span>
        <div>
            <iframe src="/images/userGuidePics/pdfUserGuide/HOW TO MAKE GC PRODUCTION REQUEST (REGULAR GC).pdf"
                style="width: 100%; height: 1000px; border: none">
            </iframe>
        </div>
    </a-card>

    <!-- How to Request Promo PDF  -->
    <a-card v-if="pdfHowToRequestPromo" style="text-align: center">
        <span style="
                font-size: 40px;
                color: #eb5b00;
                font-weight: bold;
                font-family: sans-serif;
            ">
            How to Request Promo PDF
        </span>
        <div>
            <iframe src="/images/userGuidePics/pdfUserGuide/HOW TO REQUEST PROMO.pdf"
                style="width: 100%; height: 1000px; border: none">
            </iframe>
        </div>
    </a-card>

    <!-- How to Request Special External GC PDF  -->
    <a-card v-if="pdfHowToRequestSpecialExternal" style="text-align: center">
        <span style="
                font-size: 40px;
                color: #eb5b00;
                font-weight: bold;
                font-family: sans-serif;
            ">
            How to Request Special External GC PDF
        </span>
        <div>
            <iframe src="/images/userGuidePics/pdfUserGuide/HOW TO REQUEST SPECIAL EXTERNAL GC.pdf"
                style="width: 100%; height: 1000px; border: none">
            </iframe>
        </div>
    </a-card>

    <!-- Treasury Department PDF  -->
    <a-card v-if="pdfTreasuryDepartment" style="text-align: center">
        <span style="
                font-size: 40px;
                color: #eb5b00;
                font-weight: bold;
                font-family: sans-serif;
            ">
            Treasury Department PDF
        </span>
        <div>
            <iframe src="/images/userGuidePics/pdfUserGuide/BUDGET REQUEST.pdf"
                style="width: 100%; height: 1000px; border: none">
            </iframe>
        </div>
    </a-card>

    <!-- FOOTER  -->
    <a-card style="background-color: #fbf8ef">
        <div>
            <div>
                <ul class="example-2">
                    <li class="icon-content">
                        <a href="https://twitter.com/" aria-label="LinkedIn" data-social="linkedin">
                            <div class="filled"></div>

                            <svg class="twitter" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"
                                fill="currentColor" height="1.8em">
                                <path
                                    d="M42,12.429c-1.323,0.586-2.746,0.977-4.247,1.162c1.526-0.906,2.7-2.351,3.251-4.058c-1.428,0.837-3.01,1.452-4.693,1.776C34.967,9.884,33.05,9,30.926,9c-4.08,0-7.387,3.278-7.387,7.32c0,0.572,0.067,1.129,0.193,1.67c-6.138-0.308-11.582-3.226-15.224-7.654c-0.64,1.082-1,2.349-1,3.686c0,2.541,1.301,4.778,3.285,6.096c-1.211-0.037-2.351-0.374-3.349-0.914c0,0.022,0,0.055,0,0.086c0,3.551,2.547,6.508,5.923,7.181c-0.617,0.169-1.269,0.263-1.941,0.263c-0.477,0-0.942-0.054-1.392-0.135c0.94,2.902,3.667,5.023,6.898,5.086c-2.528,1.96-5.712,3.134-9.174,3.134c-0.598,0-1.183-0.034-1.761-0.104C9.268,36.786,13.152,38,17.321,38c13.585,0,21.017-11.156,21.017-20.834c0-0.317-0.01-0.633-0.025-0.945C39.763,15.197,41.013,13.905,42,12.429">
                                </path>
                            </svg>
                        </a>
                        <div class="tooltip">Twitter</div>
                    </li>
                    <li class="icon-content">
                        <a href="https://facebook.com/" aria-label="LinkedIn" data-social="linkedin">
                            <div class="filled"></div>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" height="1.2em"
                                viewBox="0 0 320 512">
                                <path
                                    d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z">
                                </path>
                            </svg>
                        </a>
                        <div class="tooltip">Facebook</div>
                    </li>
                    <li class="icon-content">
                        <a href="https://www.github.com/" aria-label="GitHub" data-social="github">
                            <div class="filled"></div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-github" viewBox="0 0 16 16" xml:space="preserve">
                                <path
                                    d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"
                                    fill="currentColor"></path>
                            </svg>
                        </a>
                        <div class="tooltip">GitHub</div>
                    </li>
                    <li class="icon-content">
                        <a href="https://www.instagram.com/" aria-label="Instagram" data-social="instagram">
                            <div class="filled"></div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-instagram" viewBox="0 0 16 16" xml:space="preserve">
                                <path
                                    d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"
                                    fill="currentColor"></path>
                            </svg>
                        </a>
                        <div class="tooltip">Instagram</div>
                    </li>
                    <li class="icon-content">
                        <a href="https://youtube.com/" aria-label="Youtube" data-social="youtube">
                            <div class="filled"></div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-youtube" viewBox="0 0 16 16" xml:space="preserve">
                                <path
                                    d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z"
                                    fill="currentColor"></path>
                            </svg>
                        </a>
                        <div class="tooltip">Youtube</div>
                    </li>
                </ul>
            </div>
        </div>

        <h2 style="text-align: center; margin-top: 20px; color: #003140">
            Copyright © 2025 gift-check.com All Rights Reserved. - Legal
        </h2>
    </a-card>
</template>
<script setup>
import { EyeOutlined } from "@ant-design/icons-vue";
import { onMounted, onUnmounted, ref } from "vue";
const showScrollButton = ref(false);

// SCROLL UP BUTTON
const handleScroll = () => {
    if (window.scrollY > 500) {
        showScrollButton.value = true;
    } else {
        showScrollButton.value = false;
    }
};
const scrollUp = () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });
};
onMounted(() => {
    window.addEventListener("scroll", handleScroll);
});
onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});

// VIEW PER PAGE
const gcRequisitionPerStorePerPage = ref(false);
const howToMakeGCProductionPerPage = ref(false);
const howToRequestPromoPerPage = ref(false);
const howToRequestSpecialExternalPerPage = ref(false);
const treasuryDepartmentPerPage = ref(false);
const homePage = ref(true);

// VIEW PER PDF
const pdfGcProductionRegularGc = ref(false);
const pdfhowToMakeGCProductionPerPage = ref(false);
const pdfHowToRequestPromo = ref(false);
const pdfHowToRequestSpecialExternal = ref(false);
const pdfTreasuryDepartment = ref(false);
</script>

<!-- heading-button  -->
<style scoped>
.heading-button {
    font-size: 17px;
    cursor: pointer;
    text-decoration: none;
    color: #003140;
    font-weight: bold;
    font-family:
        Cambria,
        Cochin,
        Georgia,
        Times,
        Times New Roman serif;
}

.heading-button:hover {
    background-color: #20e584;
}
</style>

<!-- EXIT BUTTON  -->
<style scoped>
.animated-button {
    position: relative;
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 16px 36px;
    border: 4px solid;
    border-color: white;
    font-size: 16px;
    border-radius: 100px;
    font-weight: 600;
    color: #003140;
    box-shadow: 0 0 0 2px #fbf8ef;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.animated-button svg {
    position: absolute;
    width: 24px;
    fill: #003140;
    z-index: 9;
    transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
}

.animated-button .arr-1 {
    right: 16px;
}

.animated-button .arr-2 {
    left: -25%;
}

.animated-button .circle {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-90%, -90%);
    width: 20px;
    height: 20px;
    background-color: #20e584;
    border-radius: 100px;
    opacity: 0;
    transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
}

.animated-button .text {
    position: relative;
    z-index: 1;
    transform: translateX(-12px);
    transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
    font-family: Cambria, Cochin, Georgia, Times, "Times New Roman", serif;
}

.animated-button:hover {
    box-shadow: 0 0 0 12px transparent;
    color: #212121;
    /* border-radius: 12px; */
}

.animated-button:hover .arr-1 {
    right: -25%;
}

.animated-button:hover .arr-2 {
    left: 16px;
}

.animated-button:hover .text {
    transform: translateX(12px);
}

.animated-button:hover svg {
    fill: #003140;
}

.animated-button:active {
    scale: 0.95;
    box-shadow: 0 0 0 4px white;
}

.animated-button:hover .circle {
    width: 220px;
    height: 220px;
    opacity: 1;
}
</style>

<!-- GIFT CHECK COLORS  -->
<style scoped>
@keyframes colorWave {
    0% {
        color: #eb5b00;
    }

    25% {
        color: #525b44;
    }

    50% {
        color: #013040;
    }

    75% {
        color: #3b1c32;
    }

    100% {
        color: #eb5b00;
    }
}

.gift-check-colors {
    font-weight: bold;
    animation: colorWave 5s infinite;
    font-family: Cambria, Cochin, Georgia, Times, "Times New Roman", serif;
}
</style>

<!-- FOOTER SOCIAL MEDIA ICONS  -->
<style scoped>
ul {
    list-style: none;
}

.example-2 {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.example-2 .icon-content {
    margin: 0 10px;
    position: relative;
}

.example-2 .icon-content .tooltip {
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    padding: 6px 10px;
    border-radius: 5px;
    opacity: 0;
    visibility: hidden;
    font-size: 14px;
    transition: all 0.3s ease;
}

.example-2 .icon-content:hover .tooltip {
    opacity: 1;
    visibility: visible;
    top: -50px;
}

.example-2 .icon-content a {
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    color: #4d4d4d;
    background-color: #fff;
    transition: all 0.3s ease-in-out;
}

.example-2 .icon-content a:hover {
    box-shadow: 3px 2px 45px 0px rgb(0 0 0 / 12%);
}

.example-2 .icon-content a svg {
    position: relative;
    z-index: 1;
    width: 30px;
    height: 30px;
}

.example-2 .icon-content a:hover {
    color: white;
}

.example-2 .icon-content a .filled {
    position: absolute;
    top: auto;
    bottom: 0;
    left: 0;
    width: 400%;
    height: 0;
    background-color: #000;
    transition: all 0.3s ease-in-out;
}

.example-2 .icon-content a:hover .filled {
    height: 100%;
}

.example-2 .icon-content a[data-social="linkedin"] .filled,
.example-2 .icon-content a[data-social="linkedin"]~.tooltip {
    background-color: #0274b3;
}

.example-2 .icon-content a[data-social="github"] .filled,
.example-2 .icon-content a[data-social="github"]~.tooltip {
    background-color: #24262a;
}

.example-2 .icon-content a[data-social="instagram"] .filled,
.example-2 .icon-content a[data-social="instagram"]~.tooltip {
    background: linear-gradient(45deg,
            #405de6,
            #5b51db,
            #b33ab4,
            #c135b4,
            #e1306c,
            #fd1f1f);
}

.example-2 .icon-content a[data-social="youtube"] .filled,
.example-2 .icon-content a[data-social="youtube"]~.tooltip {
    background-color: #ff0000;
}
</style>

<!-- SCROLL UP BUTTON  -->
<style scoped>
.button {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #013040;
    border: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0px 0px 0px 4px rgba(180, 160, 255, 0.253);
    cursor: pointer;
    transition-duration: 0.3s;
    overflow: hidden;
    position: fixed;
    margin-top: 42%;
    bottom: 20px;
    right: 30px;
    z-index: 99;
}

.svgIcon {
    width: 12px;
    transition-duration: 0.3s;
}

.svgIcon path {
    fill: white;
}

.button:hover {
    width: 140px;
    border-radius: 50px;
    transition-duration: 0.3s;
    background-color: #20e584;
    align-items: center;
}

.button:hover .svgIcon {
    /* width: 20px; */
    transition-duration: 0.3s;
    transform: translateY(-200%);
}

.button::before {
    position: absolute;
    bottom: -20px;
    content: "Back to Top";
    color: black;
    /* transition-duration: .3s; */
    font-size: 0px;
}

.button:hover::before {
    font-size: 13px;
    opacity: 1;
    bottom: unset;
    /* transform: translateY(-30px); */
    transition-duration: 0.3s;
}
</style>

<!-- HOME ICON BUTTON  -->
<style scoped>
.menu {
    padding: 0.5rem;
    background-color: #fbf8ef;
    position: relative;
    display: flex;
    justify-content: center;
    border-radius: 100px;
    box-shadow: 0 10px 25px 0 rgba(#000, 0.075);
}

.link {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 70px;
    height: 50px;
    border-radius: 8px;
    position: relative;
    z-index: 1;
    overflow: hidden;
    transform-origin: center left;
    transition: width 0.2s ease-in;
    text-decoration: none;
    color: inherit;

    &:before {
        position: absolute;
        z-index: -1;
        content: "";
        display: block;
        border-radius: 8px;
        width: 100%;
        height: 100%;
        top: 0;
        transform: translateX(100%);
        transition: transform 0.2s ease-in;
        transform-origin: center right;
        background-color: #20e584;
    }

    &:hover,
    &:focus {
        outline: 0;
        width: 130px;

        &:before,
        .link-title {
            transform: translateX(0);
            opacity: 1;
        }
    }
}

.link-icon {
    width: 28px;
    height: 28px;
    display: block;
    flex-shrink: 0;
    left: 18px;
    position: absolute;

    svg {
        width: 28px;
        height: 28px;
    }
}

.link-title {
    transform: translateX(100%);
    transition: transform 0.2s ease-in;
    transform-origin: center right;
    display: block;
    text-align: center;
    text-indent: 28px;
    width: 100%;
}
</style>
