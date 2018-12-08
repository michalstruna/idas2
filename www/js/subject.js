let departments = [];

const getDepartments = async () => {
    const request = await fetch('/idas2/www/department/json');
    departments = await request.json();
};
getDepartments();