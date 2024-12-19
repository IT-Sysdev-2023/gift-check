<template>
    <header
        style="
            background-color: whitesmoke;
            color: black;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        "
    >
        <!-- Logo -->
        <div style="font-size: 24px; font-weight: bold">
            <img
                src="/images/logo.jpg"
                style="
                    border-radius: 50%;
                    width: 100px;
                    height: 100px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                "
            />
        </div>

        <!-- Navigation -->
        <nav>
            <ul
                style="
                    list-style-type: none;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    gap: 40px;
                "
            >
                <li>
                    <a
                        href="#supervisedBy"
                        style="
                            color: black;
                            font-weight: bold;
                            font-family: Cambria, Cochin, Georgia, Times,
                                &quot;Times New Roman&quot;, serif;
                        "
                    >
                        Supervised By</a
                    >
                </li>
                <li>
                    <a
                        href="#programmers"
                        style="
                            color: black;
                            font-weight: bold;
                            font-family: Cambria, Cochin, Georgia, Times,
                                &quot;Times New Roman&quot;, serif;
                        "
                        >Programmers</a
                    >
                </li>
                <li>
                    <a
                        href="#systemAnalyst"
                        style="
                            color: black;
                            font-weight: bold;
                            font-family: Cambria, Cochin, Georgia, Times,
                                &quot;Times New Roman&quot;, serif;
                        "
                        >System Analyst</a
                    >
                </li>
                <li>
                    <a
                        href="#aboutSystem"
                        style="
                            color: black;
                            font-weight: bold;
                            font-family: Cambria, Cochin, Georgia, Times,
                                &quot;Times New Roman&quot;, serif;
                        "
                        >About System</a
                    >
                </li>
            </ul>
        </nav>

        <!-- Action Button -->
        <button class="animated-button">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="arr-2"
                viewBox="0 0 24 24"
            >
                <path
                    d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                ></path>
            </svg>
            <span class="text">E X I T</span>
            <span class="circle"></span>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="arr-1"
                viewBox="0 0 24 24"
            >
                <path
                    d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                ></path>
            </svg>
        </button>
    </header>

    <!-- scroll up button  -->

    <div>
        <button class="button" @click="scrollUp" v-if="showScrollButton">
            <svg class="svgIcon" viewBox="0 0 384 512">
                <path
                    d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"
                ></path>
            </svg>
        </button>
    </div>
    <!-- headings  -->
    <div>
        <div class="welcome-message">
            <div
                style="
                    font-weight: bold;
                    font-family: Cambria, Cochin, Georgia, Times,
                        &quot;Times New Roman&quot;, serif;
                "
            >
                Hello World 👋, Welcome to
            </div>
        </div>
        <div style="text-align: center">
            <span
                style="
                    font-size: 40px;
                    color: #008bf8;
                    font-weight: bold;
                    font-family: &quot;Gill Sans&quot;,
                        &quot;Gill Sans MT&quot;, Calibri,
                        &quot;Trebuchet MS&quot;, sans-serif;
                "
            >
                GIFT CHECK
            </span>
            <span style="font-weight: bold">TEAM / FAMILY</span>
        </div>
    </div>
    <!-- quote message  -->
    <div style="text-align: center; font-weight: bold; margin-top: 20px">
        <div>
            "In all your ways acknowledge Him, and He will make straight your
            paths."
        </div>
        <div>– Proverbs 3:6</div>
    </div>
    <div
        style="
            text-align: center;
            font-weight: bold;
            font-size: large;
            font-family: Cambria, Cochin, Georgia, Times,
                &quot;Times New Roman&quot;, serif;
            margin-top: 10%;
        "
    >
        <div>SUPERVISED BY</div>
    </div>

    <!-- cards supervised by -->
    <div style="display: flex; align-items: center; justify-content: center">
        <div>
            <div class="card">
                <div>
                    <img
                        src="/images/maamFuertes.jpg"
                        class="profile-picture"
                    />
                </div>
                <div class="card-details">
                    <p class="text-title">Maria Neliza Uy Fuertes</p>
                    <p class="text-body">Group Managing Director</p>
                    <p
                        style="
                            color: rgb(134, 134, 134);
                            position: absolute;
                            top: 93%;
                            margin-left: 10px;
                        "
                    >
                        CIA, CSCU, CISA, REB, REA, CICA, CrFA
                    </p>
                </div>
                <button class="card-button">More info</button>
            </div>
        </div>
        <div style="margin-left: 10%">
            <div class="card">
                <div>
                    <img src="/images/maamTina.jpg" class="profile-picture" />
                </div>
                <div class="card-details">
                    <p class="text-title">Maria Cristina C. Evarle</p>
                    <p class="text-body">Jr Supervisor</p>
                </div>
                <button class="card-button">More info</button>
            </div>
        </div>
    </div>

    <!-- programmers block  -->
    <div
        style="
            text-align: center;
            font-weight: bold;
            font-size: large;

            font-family: Cambria, Cochin, Georgia, Times,
                &quot;Times New Roman&quot;, serif;
            margin-top: 15%;
        "
    >
        <div>PROGRAMMERS</div>
    </div>

    <!-- programmers card  -->
    <div
        style="
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 25px;
        "
    >
        <div>
            <div class="card">
                <div>
                    <img src="/images/kentoy.jpg" class="profile-picture" />
                </div>
                <div class="card-details">
                    <p class="text-title">Kent Jeremai H. Abarquez</p>
                    <p class="text-body">GC Team Leader / Programmer</p>
                </div>
                <button
                    @click="
                        () => {
                            kentModal = true;
                        }
                    "
                    class="card-button"
                >
                    More info
                </button>
            </div>
        </div>
        <div style="margin-left: 40px">
            <div class="card">
                <div>
                    <img src="/images/san.jpg" class="profile-picture" />
                </div>
                <div class="card-details">
                    <p class="text-title">Jessan P. Palban</p>
                    <p class="text-body">GC Sr / Programmer</p>
                </div>
                <button @click="() =>{jessanModal = true}" class="card-button">More info</button>
            </div>
        </div>
        <div style="margin-left: 40px">
            <div class="card">
                <div>
                    <img
                        src="/images/bibong final.jpg"
                        class="profile-picture"
                    />
                </div>
                <div class="card-details">
                    <p class="text-title">Harvey J. Barace</p>
                    <p class="text-body">GC Sr / Programmer</p>
                </div>
                <button class="card-button">More info</button>
            </div>
        </div>
        <div style="margin-left: 40px">
            <div class="card">
                <div>
                    <img src="/images/teo.jpg" class="profile-picture" />
                </div>
                <div class="card-details">
                    <p class="text-title">Teofredo M. Gamale</p>
                    <p class="text-body">GC Jr / Programmer</p>
                </div>
                <button
                    @click="
                        () => {
                            teofredoModal = true;
                        }
                    "
                    class="card-button"
                >
                    More info
                </button>
            </div>
        </div>
        <!-- kent modal  -->
        <a-modal v-model:open="kentModal" :footer="false" style="width: 45%">
            <div>
                <img src="/images/manny.webp" class="profile-picture" />
                <div class="loader">ACTIVE ...</div>

                <div
                    style="
                        font-size: larger;
                        font-weight: bold;
                        font-family: Cambria, Cochin, Georgia, Times,
                            &quot;Times New Roman&quot;, serif;
                    "
                >
                    WHO ?
                </div>
            </div>
            <a-card>
                <div
                    style="
                        font-weight: bold;
                        font-family: Cambria, Cochin, Georgia, Times,
                            &quot;Times New Roman&quot;, serif;
                        font-size: x-large;
                        text-align: center;
                        color: #008bf8;
                    "
                >
                    <span>KENT JEREMAI H. ABARQUEZ </span>
                </div>
                <div
                    style="
                        margin-top: 20px;
                        font-weight: bold;
                        font-family:
                            system-ui,
                            -apple-system,
                            BlinkMacSystemFont,
                            &quot;Segoe UI&quot;,
                            Roboto,
                            Oxygen,
                            Ubuntu,
                            Cantarell,
                            &quot;Open Sans&quot;,
                            &quot;Helvetica Neue&quot;,
                            sans-serif;
                    "
                >
                    <div>24 Years Old</div>
                    <div>Tubigon, Bohol</div>
                    <div>HEAD OFFICE-Information Technology</div>
                    <div>Taken</div>
                </div>
            </a-card>

            <div
                style="
                    font-size: large;
                    font-weight: bold;
                    font-family: Cambria, Cochin, Georgia, Times,
                        &quot;Times New Roman&quot;, serif;
                "
            >
                MOTTO IN LIFE ?
            </div>
            <a-card>
                <div
                    style="
                        font-weight: bold;
                        color: red;
                        font-family: &quot;Franklin Gothic Medium&quot;,
                            &quot;Arial Narrow&quot;, Arial, sans-serif;
                    "
                >
                    <span>MOTTO TOY</span>
                </div>
            </a-card>

            <div
                style="
                    font-size: large;
                    font-weight: bold;
                    font-family: Cambria, Cochin, Georgia, Times,
                        &quot;Times New Roman&quot;, serif;
                "
            >
                CONTACT US ?
            </div>
            <a-card>
                <div>
                    <ul class="wrapper">
                        <li class="icon facebook">
                            <span class="tooltip">Kent Abarquez</span>
                            <svg
                                viewBox="0 0 320 512"
                                height="1.2em"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"
                                ></path>
                            </svg>
                        </li>
                        <li class="icon twitter">
                            <span class="tooltip">@None</span>
                            <svg
                                height="1.8em"
                                fill="currentColor"
                                viewBox="0 0 48 48"
                                xmlns="http://www.w3.org/2000/svg"
                                class="twitter"
                            >
                                <path
                                    d="M42,12.429c-1.323,0.586-2.746,0.977-4.247,1.162c1.526-0.906,2.7-2.351,3.251-4.058c-1.428,0.837-3.01,1.452-4.693,1.776C34.967,9.884,33.05,9,30.926,9c-4.08,0-7.387,3.278-7.387,7.32c0,0.572,0.067,1.129,0.193,1.67c-6.138-0.308-11.582-3.226-15.224-7.654c-0.64,1.082-1,2.349-1,3.686c0,2.541,1.301,4.778,3.285,6.096c-1.211-0.037-2.351-0.374-3.349-0.914c0,0.022,0,0.055,0,0.086c0,3.551,2.547,6.508,5.923,7.181c-0.617,0.169-1.269,0.263-1.941,0.263c-0.477,0-0.942-0.054-1.392-0.135c0.94,2.902,3.667,5.023,6.898,5.086c-2.528,1.96-5.712,3.134-9.174,3.134c-0.598,0-1.183-0.034-1.761-0.104C9.268,36.786,13.152,38,17.321,38c13.585,0,21.017-11.156,21.017-20.834c0-0.317-0.01-0.633-0.025-0.945C39.763,15.197,41.013,13.905,42,12.429"
                                ></path>
                            </svg>
                        </li>
                        <li class="icon instagram">
                            <span class="tooltip">None</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                height="1.2em"
                                fill="currentColor"
                                class="bi bi-instagram"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"
                                ></path>
                            </svg>
                        </li>
                    </ul>
                </div>
                <div>
                    <ul class="example-2">
                        <li class="icon-content">
                            <a
                                href="https://linkedin.com/"
                                aria-label="LinkedIn"
                                data-social="linkedin"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-linkedin"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">None</div>
                        </li>
                        <li class="icon-content">
                            <a
                                href="https://www.github.com/"
                                aria-label="GitHub"
                                data-social="github"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-github"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">None</div>
                        </li>
                        <li class="icon-content">
                            <a
                                href="https://youtube.com/"
                                aria-label="Youtube"
                                data-social="youtube"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-youtube"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">None</div>
                        </li>
                    </ul>
                </div>
            </a-card>
        </a-modal>

        <!-- jessan Modal  -->
        <a-modal v-model:open="jessanModal" :footer="false" style="width: 45%">
            <div>
                <img src="/images/bossing.webp" class="profile-picture" />
                <div class="loader">ACTIVE ...</div>

                <div
                    style="
                        font-size: larger;
                        font-weight: bold;
                        font-family: Cambria, Cochin, Georgia, Times,
                            &quot;Times New Roman&quot;, serif;
                    "
                >
                    WHO ?
                </div>
            </div>
            <a-card>
                <div
                    style="
                        font-weight: bold;
                        font-family: Cambria, Cochin, Georgia, Times,
                            &quot;Times New Roman&quot;, serif;
                        font-size: x-large;
                        text-align: center;
                        color: #008bf8;
                    "
                >
                    <span>JESSAN P. PALBAN </span>
                </div>
                <div
                    style="
                        margin-top: 20px;
                        font-weight: bold;
                        font-family:
                            system-ui,
                            -apple-system,
                            BlinkMacSystemFont,
                            &quot;Segoe UI&quot;,
                            Roboto,
                            Oxygen,
                            Ubuntu,
                            Cantarell,
                            &quot;Open Sans&quot;,
                            &quot;Helvetica Neue&quot;,
                            sans-serif;
                    "
                >
                    <div>24 Years Old</div>
                    <div>Taga-ilaha, Bohol</div>
                    <div>HEAD OFFICE-Information Technology</div>
                    <div>Taken</div>
                </div>
            </a-card>

            <div
                style="
                    font-size: large;
                    font-weight: bold;
                    font-family: Cambria, Cochin, Georgia, Times,
                        &quot;Times New Roman&quot;, serif;
                "
            >
                MOTTO IN LIFE ?
            </div>
            <a-card>
                <div
                    style="
                        font-weight: bold;
                        color: red;
                        font-family: &quot;Franklin Gothic Medium&quot;,
                            &quot;Arial Narrow&quot;, Arial, sans-serif;
                    "
                >
                    <span>MOTTO LOVER</span>
                </div>
            </a-card>

            <div
                style="
                    font-size: large;
                    font-weight: bold;
                    font-family: Cambria, Cochin, Georgia, Times,
                        &quot;Times New Roman&quot;, serif;
                "
            >
                CONTACT US ?
            </div>
            <a-card>
                <div>
                    <ul class="wrapper">
                        <li class="icon facebook">
                            <span class="tooltip">Jessan Palban</span>
                            <svg
                                viewBox="0 0 320 512"
                                height="1.2em"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"
                                ></path>
                            </svg>
                        </li>
                        <li class="icon twitter">
                            <span class="tooltip">@None</span>
                            <svg
                                height="1.8em"
                                fill="currentColor"
                                viewBox="0 0 48 48"
                                xmlns="http://www.w3.org/2000/svg"
                                class="twitter"
                            >
                                <path
                                    d="M42,12.429c-1.323,0.586-2.746,0.977-4.247,1.162c1.526-0.906,2.7-2.351,3.251-4.058c-1.428,0.837-3.01,1.452-4.693,1.776C34.967,9.884,33.05,9,30.926,9c-4.08,0-7.387,3.278-7.387,7.32c0,0.572,0.067,1.129,0.193,1.67c-6.138-0.308-11.582-3.226-15.224-7.654c-0.64,1.082-1,2.349-1,3.686c0,2.541,1.301,4.778,3.285,6.096c-1.211-0.037-2.351-0.374-3.349-0.914c0,0.022,0,0.055,0,0.086c0,3.551,2.547,6.508,5.923,7.181c-0.617,0.169-1.269,0.263-1.941,0.263c-0.477,0-0.942-0.054-1.392-0.135c0.94,2.902,3.667,5.023,6.898,5.086c-2.528,1.96-5.712,3.134-9.174,3.134c-0.598,0-1.183-0.034-1.761-0.104C9.268,36.786,13.152,38,17.321,38c13.585,0,21.017-11.156,21.017-20.834c0-0.317-0.01-0.633-0.025-0.945C39.763,15.197,41.013,13.905,42,12.429"
                                ></path>
                            </svg>
                        </li>
                        <li class="icon instagram">
                            <span class="tooltip">None</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                height="1.2em"
                                fill="currentColor"
                                class="bi bi-instagram"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"
                                ></path>
                            </svg>
                        </li>
                    </ul>
                </div>
                <div>
                    <ul class="example-2">
                        <li class="icon-content">
                            <a
                                href="https://linkedin.com/"
                                aria-label="LinkedIn"
                                data-social="linkedin"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-linkedin"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">None</div>
                        </li>
                        <li class="icon-content">
                            <a
                                href="https://www.github.com/"
                                aria-label="GitHub"
                                data-social="github"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-github"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">None</div>
                        </li>
                        <li class="icon-content">
                            <a
                                href="https://youtube.com/"
                                aria-label="Youtube"
                                data-social="youtube"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-youtube"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">None</div>
                        </li>
                    </ul>
                </div>
            </a-card>
        </a-modal>

        <!-- bibong modal  -->
<a-modal v-model:open="jessanModal" :footer="false" style="width: 45%">
            <div>
                <img src="/images/bossing.webp" class="profile-picture" />
                <div class="loader">ACTIVE ...</div>

                <div
                    style="
                        font-size: larger;
                        font-weight: bold;
                        font-family: Cambria, Cochin, Georgia, Times,
                            &quot;Times New Roman&quot;, serif;
                    "
                >
                    WHO ?
                </div>
            </div>
            <a-card>
                <div
                    style="
                        font-weight: bold;
                        font-family: Cambria, Cochin, Georgia, Times,
                            &quot;Times New Roman&quot;, serif;
                        font-size: x-large;
                        text-align: center;
                        color: #008bf8;
                    "
                >
                    <span>JESSAN P. PALBAN </span>
                </div>
                <div
                    style="
                        margin-top: 20px;
                        font-weight: bold;
                        font-family:
                            system-ui,
                            -apple-system,
                            BlinkMacSystemFont,
                            &quot;Segoe UI&quot;,
                            Roboto,
                            Oxygen,
                            Ubuntu,
                            Cantarell,
                            &quot;Open Sans&quot;,
                            &quot;Helvetica Neue&quot;,
                            sans-serif;
                    "
                >
                    <div>24 Years Old</div>
                    <div>Taga-ilaha, Bohol</div>
                    <div>HEAD OFFICE-Information Technology</div>
                    <div>Taken</div>
                </div>
            </a-card>

            <div
                style="
                    font-size: large;
                    font-weight: bold;
                    font-family: Cambria, Cochin, Georgia, Times,
                        &quot;Times New Roman&quot;, serif;
                "
            >
                MOTTO IN LIFE ?
            </div>
            <a-card>
                <div
                    style="
                        font-weight: bold;
                        color: red;
                        font-family: &quot;Franklin Gothic Medium&quot;,
                            &quot;Arial Narrow&quot;, Arial, sans-serif;
                    "
                >
                    <span>MOTTO LOVER</span>
                </div>
            </a-card>

            <div
                style="
                    font-size: large;
                    font-weight: bold;
                    font-family: Cambria, Cochin, Georgia, Times,
                        &quot;Times New Roman&quot;, serif;
                "
            >
                CONTACT US ?
            </div>
            <a-card>
                <div>
                    <ul class="wrapper">
                        <li class="icon facebook">
                            <span class="tooltip">Jessan Palban</span>
                            <svg
                                viewBox="0 0 320 512"
                                height="1.2em"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"
                                ></path>
                            </svg>
                        </li>
                        <li class="icon twitter">
                            <span class="tooltip">@None</span>
                            <svg
                                height="1.8em"
                                fill="currentColor"
                                viewBox="0 0 48 48"
                                xmlns="http://www.w3.org/2000/svg"
                                class="twitter"
                            >
                                <path
                                    d="M42,12.429c-1.323,0.586-2.746,0.977-4.247,1.162c1.526-0.906,2.7-2.351,3.251-4.058c-1.428,0.837-3.01,1.452-4.693,1.776C34.967,9.884,33.05,9,30.926,9c-4.08,0-7.387,3.278-7.387,7.32c0,0.572,0.067,1.129,0.193,1.67c-6.138-0.308-11.582-3.226-15.224-7.654c-0.64,1.082-1,2.349-1,3.686c0,2.541,1.301,4.778,3.285,6.096c-1.211-0.037-2.351-0.374-3.349-0.914c0,0.022,0,0.055,0,0.086c0,3.551,2.547,6.508,5.923,7.181c-0.617,0.169-1.269,0.263-1.941,0.263c-0.477,0-0.942-0.054-1.392-0.135c0.94,2.902,3.667,5.023,6.898,5.086c-2.528,1.96-5.712,3.134-9.174,3.134c-0.598,0-1.183-0.034-1.761-0.104C9.268,36.786,13.152,38,17.321,38c13.585,0,21.017-11.156,21.017-20.834c0-0.317-0.01-0.633-0.025-0.945C39.763,15.197,41.013,13.905,42,12.429"
                                ></path>
                            </svg>
                        </li>
                        <li class="icon instagram">
                            <span class="tooltip">None</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                height="1.2em"
                                fill="currentColor"
                                class="bi bi-instagram"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"
                                ></path>
                            </svg>
                        </li>
                    </ul>
                </div>
                <div>
                    <ul class="example-2">
                        <li class="icon-content">
                            <a
                                href="https://linkedin.com/"
                                aria-label="LinkedIn"
                                data-social="linkedin"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-linkedin"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">None</div>
                        </li>
                        <li class="icon-content">
                            <a
                                href="https://www.github.com/"
                                aria-label="GitHub"
                                data-social="github"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-github"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">None</div>
                        </li>
                        <li class="icon-content">
                            <a
                                href="https://youtube.com/"
                                aria-label="Youtube"
                                data-social="youtube"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-youtube"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">None</div>
                        </li>
                    </ul>
                </div>
            </a-card>
        </a-modal>


        <!-- teofredo modal  -->
        <a-modal
            v-model:open="teofredoModal"
            :footer="false"
            style="width: 45%"
        >
            <div>
                <img src="/images/jonny.webp" class="profile-picture" />
                <div class="loader">ACTIVE ...</div>

                <div
                    style="
                        font-size: larger;
                        font-weight: bold;
                        font-family: Cambria, Cochin, Georgia, Times,
                            &quot;Times New Roman&quot;, serif;
                    "
                >
                    WHO ?
                </div>
            </div>
            <a-card>
                <div
                    style="
                        font-weight: bold;
                        font-family: Cambria, Cochin, Georgia, Times,
                            &quot;Times New Roman&quot;, serif;
                        font-size: x-large;
                        text-align: center;
                        color: #008bf8;
                    "
                >
                    <span>TEOFREDO M. GAMALE </span>
                </div>
                <div
                    style="
                        margin-top: 20px;
                        font-weight: bold;
                        font-family:
                            system-ui,
                            -apple-system,
                            BlinkMacSystemFont,
                            &quot;Segoe UI&quot;,
                            Roboto,
                            Oxygen,
                            Ubuntu,
                            Cantarell,
                            &quot;Open Sans&quot;,
                            &quot;Helvetica Neue&quot;,
                            sans-serif;
                    "
                >
                    <div>23 Years Old</div>
                    <div>Owac, Bilar, Bohol</div>
                    <div>HEAD OFFICE-Information Technology</div>
                    <div>Single</div>
                </div>
            </a-card>

            <div
                style="
                    font-size: large;
                    font-weight: bold;
                    font-family: Cambria, Cochin, Georgia, Times,
                        &quot;Times New Roman&quot;, serif;
                "
            >
                MOTTO IN LIFE ?
            </div>
            <a-card>
                <div
                    style="
                        font-weight: bold;
                        color: red;
                        font-family: &quot;Franklin Gothic Medium&quot;,
                            &quot;Arial Narrow&quot;, Arial, sans-serif;
                    "
                >
                    <span
                        >Ingon sila kuan daw, so mao to akoa nalang gi kuan kay
                        ingon man siya so mao rato. Pero lain sad bitaw pud
                        no?</span
                    >
                </div>
            </a-card>

            <div
                style="
                    font-size: large;
                    font-weight: bold;
                    font-family: Cambria, Cochin, Georgia, Times,
                        &quot;Times New Roman&quot;, serif;
                "
            >
                CONTACT US ?
            </div>
            <a-card>
                <div>
                    <ul class="wrapper">
                        <li class="icon facebook">
                            <span class="tooltip">Teofredo M. Gamale</span>
                            <svg
                                viewBox="0 0 320 512"
                                height="1.2em"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"
                                ></path>
                            </svg>
                        </li>
                        <li class="icon twitter">
                            <span class="tooltip">@Bossing_Kupal</span>
                            <svg
                                height="1.8em"
                                fill="currentColor"
                                viewBox="0 0 48 48"
                                xmlns="http://www.w3.org/2000/svg"
                                class="twitter"
                            >
                                <path
                                    d="M42,12.429c-1.323,0.586-2.746,0.977-4.247,1.162c1.526-0.906,2.7-2.351,3.251-4.058c-1.428,0.837-3.01,1.452-4.693,1.776C34.967,9.884,33.05,9,30.926,9c-4.08,0-7.387,3.278-7.387,7.32c0,0.572,0.067,1.129,0.193,1.67c-6.138-0.308-11.582-3.226-15.224-7.654c-0.64,1.082-1,2.349-1,3.686c0,2.541,1.301,4.778,3.285,6.096c-1.211-0.037-2.351-0.374-3.349-0.914c0,0.022,0,0.055,0,0.086c0,3.551,2.547,6.508,5.923,7.181c-0.617,0.169-1.269,0.263-1.941,0.263c-0.477,0-0.942-0.054-1.392-0.135c0.94,2.902,3.667,5.023,6.898,5.086c-2.528,1.96-5.712,3.134-9.174,3.134c-0.598,0-1.183-0.034-1.761-0.104C9.268,36.786,13.152,38,17.321,38c13.585,0,21.017-11.156,21.017-20.834c0-0.317-0.01-0.633-0.025-0.945C39.763,15.197,41.013,13.905,42,12.429"
                                ></path>
                            </svg>
                        </li>
                        <li class="icon instagram">
                            <span class="tooltip">Teofredo M. Gamale</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                height="1.2em"
                                fill="currentColor"
                                class="bi bi-instagram"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"
                                ></path>
                            </svg>
                        </li>
                    </ul>
                </div>
                <div>
                    <ul class="example-2">
                        <li class="icon-content">
                            <a
                                href="https://linkedin.com/"
                                aria-label="LinkedIn"
                                data-social="linkedin"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-linkedin"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">Hi Baby</div>
                        </li>
                        <li class="icon-content">
                            <a
                                href="https://www.github.com/"
                                aria-label="GitHub"
                                data-social="github"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-github"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">Kupalkaba1</div>
                        </li>
                        <li class="icon-content">
                            <a
                                href="https://youtube.com/"
                                aria-label="Youtube"
                                data-social="youtube"
                            >
                                <div class="filled"></div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-youtube"
                                    viewBox="0 0 16 16"
                                    xml:space="preserve"
                                >
                                    <path
                                        d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </a>
                            <div class="tooltip">Bossing Tv</div>
                        </li>
                    </ul>
                </div>
            </a-card>
        </a-modal>
    </div>
    <!-- system analyst block  -->
    <div
        style="
            text-align: center;
            font-weight: bold;
            font-size: large;

            font-family: Cambria, Cochin, Georgia, Times,
                &quot;Times New Roman&quot;, serif;
            margin-top: 15%;
        "
    >
        <div>SYSTEM ANALYST</div>
    </div>

    <!-- system analyst  -->

    <div
        style="
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 25px;
        "
    >
        <div>
            <div class="card">
                <div>
                    <img src="/images/claire.jpg" class="profile-picture" />
                </div>
                <div class="card-details">
                    <p class="text-title">Claire Joy B. Cagas</p>
                    <p class="text-body">GC System Analyst</p>
                </div>
                <button class="card-button">More info</button>
            </div>
        </div>
        <div style="margin-left: 40px">
            <div class="card">
                <div>
                    <img src="/images/noriens.jpg" class="profile-picture" />
                </div>
                <div class="card-details">
                    <p class="text-title">Caren S. Norien</p>
                    <p class="text-body">GC System Analyst</p>
                </div>
                <button class="card-button">More info</button>
            </div>
        </div>
        <div style="margin-left: 40px">
            <div class="card">
                <div>
                    <img src="/images/mariscel.jpg" class="profile-picture" />
                </div>
                <div class="card-details">
                    <p class="text-title">Maricel D. Dupit</p>
                    <p class="text-body">GC System Analyst</p>
                </div>
                <button class="card-button">More info</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from "@inertiajs/core";
import { onMounted, ref, onUnmounted } from "vue";

const showScrollButton = ref(false);

const handleScroll = () => {
    showScrollButton.value = window.scrollY > 450;
};
const scrollUp = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});

// infos modal
const teofredoModal = ref(false);
const kentModal = ref(false);
const jessanModal = ref(false);
</script>

<!-- /* exit button */ -->
<style scoped>
.animated-button {
    position: relative;
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 16px 36px;
    border: 4px solid;
    border-color: transparent;
    font-size: 16px;
    border-radius: 100px;
    font-weight: 600;
    color: #1f387e;
    box-shadow: 0 0 0 2px #ffffff;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.animated-button svg {
    position: absolute;
    width: 24px;
    fill: #1f387e;
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
    transform: translate(-100%, -100%);
    width: 20px;
    height: 20px;
    background-color: white;
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
    border-radius: 12px;
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
    fill: #1f387e;
}

.animated-button:active {
    scale: 0.95;
    box-shadow: 0 0 0 4px #1e90ff;
}

.animated-button:hover .circle {
    width: 220px;
    height: 220px;
    opacity: 1;
}
</style>

<!-- /* cards */ -->
<style scoped>
.card {
    width: 350px;
    height: 450px;
    border-radius: 20px;
    background: #f5f5f5;
    position: relative;
    padding: 1.8rem;
    border: 2px solid #c3c6ce;
    transition: 0.5s ease-out;
    overflow: visible;
}

.card-details {
    color: black;
    height: 30%;
    gap: 0.5em;
    display: grid;
    place-content: center;
}

.card-button {
    transform: translate(-50%, 125%);
    width: 60%;
    border-radius: 1rem;
    border: none;
    background-color: #008bf8;
    color: #fff;
    font-size: 1rem;
    padding: 0.5rem 1rem;
    position: absolute;
    left: 50%;
    bottom: 0;
    opacity: 0;
    transition: 0.3s ease-out;
}

.text-body {
    text-align: center;
    color: rgb(134, 134, 134);
}

/*Text*/
.text-title {
    text-align: center;
    font-size: 1.5em;
    font-weight: bold;
}

/*Hover*/
.card:hover {
    border-color: #008bf8;
    box-shadow: 0 4px 18px 0 rgba(0, 0, 0, 0.25);
}

.card:hover .card-button {
    transform: translate(-50%, 50%);
    opacity: 1;
}
</style>

<!-- welcome message  -->
<style scoped>
.welcome-message {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    margin-top: 20px;
    font-size: 50px;
}
</style>

<!-- profile picture  -->
<style scoped>
.profile-picture {
    border-radius: 50%;
    width: 250px;
    height: 300px;
    display: block;
    margin: 0 auto;
}
</style>

<!-- scroll up button  -->
<style scoped>
/* From Uiverse.io by vinodjangid07 */
.button {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: rgb(20, 20, 20);
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
    background-color: #008bf8;
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
    color: white;
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

<!-- status  -->
<style scoped>
/* From Uiverse.io by omar-alghaish */
@keyframes animate8345 {
    0%,
    100% {
        filter: hue-rotate(0deg);
    }

    50% {
        filter: hue-rotate(360deg);
    }
}

.loader {
    color: rgb(0, 0, 0);
    margin-left: 60%;
    background: linear-gradient(to right, #2d60ec, #3ccfda);
    font-size: 20px;
    -webkit-text-fill-color: transparent;
    -webkit-background-clip: text;
    animation: animate8345 9s linear infinite;
    font-weight: bold;
}
</style>

<!-- contact us  -->
<style scoped>
/* From Uiverse.io by david-mohseni */
.wrapper {
    display: inline-flex;
    list-style: none;
    height: 40px;
    width: 100%;
    font-family: "Poppins", sans-serif;
    justify-content: center;
}

.wrapper .icon {
    position: relative;
    background: #fff;
    border-radius: 50%;
    margin: 10px;
    width: 50px;
    height: 50px;
    font-size: 18px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.wrapper .tooltip {
    position: absolute;
    width: 300%;
    top: 0;
    font-size: 14px;
    background: #fff;
    color: #fff;
    padding: 5px 8px;
    border-radius: 5px;
    box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.wrapper .tooltip::before {
    position: absolute;
    content: "";
    height: 8px;
    width: 8px;
    background: #fff;
    bottom: -3px;
    left: 50%;
    transform: translate(-50%) rotate(45deg);
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.wrapper .icon:hover .tooltip {
    top: -45px;
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.wrapper .icon:hover span,
.wrapper .icon:hover .tooltip {
    text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.1);
}

.wrapper .facebook:hover,
.wrapper .facebook:hover .tooltip,
.wrapper .facebook:hover .tooltip::before {
    background: #1877f2;
    color: #fff;
}

.wrapper .twitter:hover,
.wrapper .twitter:hover .tooltip,
.wrapper .twitter:hover .tooltip::before {
    background: #1da1f2;
    color: #fff;
}

.wrapper .instagram:hover,
.wrapper .instagram:hover .tooltip,
.wrapper .instagram:hover .tooltip::before {
    background: #e4405f;
    color: #fff;
}
</style>

<!-- github  -->
<style scoped>
/* From Uiverse.io by wilsondesouza */
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
.example-2 .icon-content a[data-social="linkedin"] ~ .tooltip {
    background-color: #0274b3;
}

.example-2 .icon-content a[data-social="github"] .filled,
.example-2 .icon-content a[data-social="github"] ~ .tooltip {
    background-color: #24262a;
}
.example-2 .icon-content a[data-social="instagram"] .filled,
.example-2 .icon-content a[data-social="instagram"] ~ .tooltip {
    background: linear-gradient(
        45deg,
        #405de6,
        #5b51db,
        #b33ab4,
        #c135b4,
        #e1306c,
        #fd1f1f
    );
}
.example-2 .icon-content a[data-social="youtube"] .filled,
.example-2 .icon-content a[data-social="youtube"] ~ .tooltip {
    background-color: #ff0000;
}
</style>
