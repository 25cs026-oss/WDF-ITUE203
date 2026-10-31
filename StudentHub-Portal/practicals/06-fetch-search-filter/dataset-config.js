'use strict';

window.studentHubDatasetConfig = {
  students: {
    file: 'students.json',
    label: 'Students',
    filterKey: 'course',
    searchKeys: ['full_name', 'email', 'course', 'year'],
    sortKeys: [
      ['full_name', 'Name'],
      ['email', 'Email'],
      ['course', 'Course'],
      ['year', 'Year']
    ],
    columns: [
      ['full_name', 'Name'],
      ['email', 'Email'],
      ['course', 'Course'],
      ['year', 'Year']
    ]
  },
  events: {
    file: 'events.json',
    label: 'Events',
    filterKey: 'category',
    searchKeys: ['title', 'date', 'location', 'category'],
    sortKeys: [
      ['title', 'Title'],
      ['date', 'Date'],
      ['location', 'Location'],
      ['category', 'Category']
    ],
    columns: [
      ['title', 'Title'],
      ['date', 'Date'],
      ['location', 'Location'],
      ['category', 'Category']
    ]
  },
  faqs: {
    file: 'faqs.json',
    label: 'FAQs',
    filterKey: 'category',
    searchKeys: ['question', 'answer', 'category'],
    sortKeys: [
      ['question', 'Question'],
      ['category', 'Category'],
      ['answer', 'Answer']
    ],
    columns: [
      ['question', 'Question'],
      ['answer', 'Answer'],
      ['category', 'Category']
    ]
  }
};
