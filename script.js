/* ===============================
   ELEMENT REFERENCES
================================ */
const incomeBTN   = document.querySelector(".incomes-btn");
const expencesBTN = document.querySelector(".expenses-btn");

const incomeForm   = document.getElementById("incomes");
const expencesForm = document.getElementById("expences");

const contInc = document.getElementById("cont_inc");
const contExp = document.getElementById("cont_exp");

const logoutBtn = document.getElementById("logout-btn");
const logoutModal = document.getElementById("logout-modal");
const cancelLogoutBtn = document.getElementById("cancel-logout");
const logoutBg = document.getElementById("logout-bg");



// ===============================
// LOGOUT MODAL
// ===============================
logoutBtn?.addEventListener("click", () => {
    logoutModal.classList.remove("hidden");
});

cancelLogoutBtn?.addEventListener("click", () => {
    logoutModal.classList.add("hidden");
});

logoutBg?.addEventListener("click", () => {
    logoutModal.classList.add("hidden");
});

document.querySelector("#logout-modal .cont")?.addEventListener("click", e => {
    e.stopPropagation();
});

/* ===============================
   OPEN ADD MODALS
================================ */
incomeBTN?.addEventListener("click", () => {
    incomeForm.classList.remove("hidden");
});

expencesBTN?.addEventListener("click", () => {
    expencesForm.classList.remove("hidden");
});

/* ===============================
   CLOSE MODALS (CLICK BACKDROP)
================================ */
document.addEventListener("click", (e) => {
    if (e.target.classList.contains("bgblur")) {
        incomeForm?.classList.add("hidden");
        expencesForm?.classList.add("hidden");
        document.querySelectorAll(".dynamic-modal").forEach(m => m.remove());
    }
});

/* ===============================
   STOP PROPAGATION INSIDE MODALS
================================ */
document.addEventListener("click", (e) => {
    if (e.target.closest(".cont")) {
        e.stopPropagation();
    }
});