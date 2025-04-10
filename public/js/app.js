document.addEventListener('DOMContentLoaded', async() => {
    try {
        const categories = await API.getCategories();
        const courses = await API.getCourses();

        window.allCategories = categories;
        window.allCourses = courses;

        renderCategoryTree(categories, courses);
        renderCourses(courses);
        setupEventListeners();

        document.querySelector('.category-item[data-id="all"]').classList.add('selected');
    } catch (error) {
        console.error('Failed to initialize app:', error);
    }
});

function buildCategoryTree(categories, parentId = null, level = 0) {
    if (level > 4) return [];

    return categories
        .filter(category => category.parent_id === parentId)
        .map(category => {
            return {
                ...category,
                children: buildCategoryTree(categories, category.id, level + 1)
            };
        });
}

function countCourses(categoryId, categories, courses) {
    const directCount = courses.filter(course => course.category_id === categoryId).length;

    const childCategories = categories.filter(cat => cat.parent_id === categoryId);

    const childCount = childCategories.reduce((sum, child) => {
        return sum + countCourses(child.id, categories, courses);
    }, 0);

    return directCount + childCount;
}

function renderCategoryTree(categories, courses) {
    const treeContainer = document.getElementById('categories-tree');

    const rootCategories = categories.filter(cat => cat.parent_id === null);

    let html = '';

    rootCategories.forEach(category => {
        html += renderCategoryItem(category, categories, courses, 0);
    });

    treeContainer.innerHTML = html;
}

function renderCategoryItem(category, allCategories, courses, level) {
    const count = countCourses(category.id, allCategories, courses);
    const children = allCategories.filter(cat => cat.parent_id === category.id);

    let html = `
        <div class="category-item${level > 0 ? ' sub-category' : ''}" data-id="${category.id}">
            <span class="category-name">${category.name}</span>
            ${count > 0 ? `<span class="category-count">(${count})</span>` : ''}
        </div>
    `;
    
    children.forEach(child => {
        html += renderCategoryItem(child, allCategories, courses, level + 1);
    });
    
    return html;
}

function findMainCategory(categoryId) {
    let category = window.allCategories.find(c => c.id === categoryId);
    
    if (!category) return 'Uncategorized';
    
    while (category.parent_id !== null) {
        const parent = window.allCategories.find(c => c.id === category.parent_id);
        if (!parent) break;
        category = parent;
    }
    
    return category.name;
}

function renderCourses(courses, categoryId = null) {
    const container = document.getElementById('courses-container');
    let filteredCourses = courses;
    
    if (categoryId && categoryId !== 'all') {
        const childCategoryIds = getAllChildCategoryIds(categoryId);
        filteredCourses = courses.filter(course => 
            course.category_id === categoryId || 
            childCategoryIds.includes(course.category_id)
        );
        
        const category = window.allCategories.find(c => c.id === categoryId);
        if (category) {
            document.getElementById('page-title').textContent = category.name;
        }
    } else {
        document.getElementById('page-title').textContent = 'Course catalog';
    }
    
    let html = '';
    
    filteredCourses.forEach(course => {
        const category = window.allCategories.find(c => c.id === course.category_id);
        const categoryName = category ? category.name : 'Uncategorized';
        const mainCategoryName = findMainCategory(course.category_id);
        
        const imageUrl = course.preview ? course.preview : `/api/placeholder/400/180`;
        
        html += `
            <div class="course-card">
                <div class="course-image" style="background-image: url('${imageUrl}')">
                    <div class="course-category-badge ${mainCategoryName}">${mainCategoryName}</div>
                </div>
                <div class="course-content">
                    <h3 class="course-title">${course.name || 'Untitled Course'}</h3>
                    <p class="course-description">${course.description || ''}</p>
                </div>
            </div>
        `;
    });
    
    if (!html) {
        html = '<p>No courses found in this category.</p>';
    }
    
    container.innerHTML = html;
}

function getAllChildCategoryIds(categoryId) {
    const allCategories = window.allCategories || [];
    const directChildren = allCategories.filter(cat => cat.parent_id === categoryId);
    
    if (directChildren.length === 0) return [];
    
    const directChildIds = directChildren.map(cat => cat.id);
    
    const nestedChildIds = directChildren.flatMap(child => 
        getAllChildCategoryIds(child.id)
    );
    
    return [...directChildIds, ...nestedChildIds];
}

function setupEventListeners() {
    document.addEventListener('click', event => {
        const categoryItem = event.target.closest('.category-item');
        if (!categoryItem) return;
        
        document.querySelectorAll('.category-item').forEach(item => {
            item.classList.remove('selected');
        });
        
        categoryItem.classList.add('selected');
        
        const categoryId = categoryItem.dataset.id;
        
        renderCourses(window.allCourses, categoryId === 'all' ? null : categoryId);
    });
}