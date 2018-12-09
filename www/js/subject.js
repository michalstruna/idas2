const teachingSelect = document.getElementsByName('teaching')[0];
const courseTypeSelect = document.getElementsByName('courseType')[0];

let teachingsLoaded = false;
let courseTypesInPlanLoaded = false;

const onCourseTypeChanged = function () {
    const selectedType = courseTypesInPlan.find((item, index) => {
        return item.id === courseTypeSelect.options[courseTypeSelect.selectedIndex].value
    });

    let html = '';

    for (let i = 0; i < teachings.length; i++) {
        const teaching = teachings[i];
        if (teaching.predm_plan_id === selectedType.predm_plan_id){
            html += '<option value="' + teaching.id + '">' + teaching['ucitel'] + ', ' + teaching['role'] + ', ' + teaching['predmet']; + '</option>';
        }
    }

    teachingSelect.innerHTML = html;
};

let teachings = [];

const getTeachings = async () => {
    const request = await fetch('/idas2/www/teaching/json');
    teachings = await request.json();
    teachingsLoaded = true;
    if (teachingsLoaded && courseTypesInPlanLoaded) {
        onCourseTypeChanged();
    }
};
getTeachings();

let courseTypesInPlan = [];

const getCourseTypesInPlan = async () => {
    const request = await fetch('/idas2/www/course-type-in-plan/json');
    courseTypesInPlan = await request.json();
    courseTypesInPlanLoaded = true;
    if (teachingsLoaded && courseTypesInPlanLoaded) {
        onCourseTypeChanged();
    }
};
getCourseTypesInPlan();

courseTypeSelect.onchange = onCourseTypeChanged;