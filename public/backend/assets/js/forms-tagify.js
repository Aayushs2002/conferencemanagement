/**
 * Tagify
 */

'use strict';

(function () {
  // Basic
  const tagifyBasicEl = document.querySelector('#TagifyBasic');
  if (tagifyBasicEl) {
    const TagifyBasic = new Tagify(tagifyBasicEl);
  }

  // Read only
  const tagifyReadonlyEl = document.querySelector('#TagifyReadonly');
  if (tagifyReadonlyEl) {
    const TagifyReadonly = new Tagify(tagifyReadonlyEl);
  }

  // Custom list & inline suggestion
  const tagifyCustomInlineSuggestionEl = document.querySelector('#TagifyCustomInlineSuggestion');
  const tagifyCustomListSuggestionEl = document.querySelector('#TagifyCustomListSuggestion');

  const whitelist = [
    'A# .NET',
    'A# (Axiom)',
    'A-0 System',
    'A+',
    'A++',
    'ABAP',
    'ABC',
    'ABC ALGOL',
    'ABSET',
    'ABSYS',
    'ACC',
    'Accent',
    'Ace DASL',
    'ACL2',
    'Avicsoft',
    'ACT-III',
    'Action!',
    'ActionScript',
    'Ada',
    'Adenine',
    'Agda',
    'Agilent VEE',
    'Agora',
    'AIMMS',
    'Alef',
    'ALF',
    'ALGOL 58',
    'ALGOL 60',
    'ALGOL 68',
    'ALGOL W',
    'Alice',
    'Alma-0',
    'AmbientTalk',
    'Amiga E',
    'AMOS',
    'AMPL',
    'Apex (Salesforce.com)',
    'APL',
    'AppleScript',
    'Arc',
    'ARexx',
    'Argus',
    'AspectJ',
    'Assembly language',
    'ATS',
    'Ateji PX',
    'AutoHotkey',
    'Autocoder',
    'AutoIt',
    'AutoLISP / Visual LISP',
    'Averest',
    'AWK',
    'Axum',
    'Active Server Pages',
    'ASP.NET'
  ];

  // Inline
  if (tagifyCustomInlineSuggestionEl) {
    const tagifyCustomInlineSuggestion = new Tagify(tagifyCustomInlineSuggestionEl, {
      whitelist: whitelist,
      maxTags: 10, 
      dropdown: {
        maxItems: 20,
        classname: 'tags-inline',
        enabled: 0,
        closeOnSelect: false
      }
    });
  }

  // List
  if (tagifyCustomListSuggestionEl) {
    const tagifyCustomListSuggestion = new Tagify(tagifyCustomListSuggestionEl, {
      whitelist: whitelist,
      maxTags: 10,
      dropdown: {
        maxItems: 20,
        classname: '',
        enabled: 0,
        closeOnSelect: false
      }
    });
  } 

  // Users List suggestion
  const tagifyUserListEl = document.querySelector('#TagifyUserList');
  console.log(tagifyUserListEl);
  const usersList = [
    {
      value: 1,
      name: 'Justinian Hattersley',
      avatar: 'https://i.pravatar.cc/80?img=1',
      email: 'jhattersley0@ucsd.edu'
    },
    { value: 2, name: 'Antons Esson', avatar: 'https://i.pravatar.cc/80?img=2', email: 'aesson1@ning.com' },
    { value: 3, name: 'Ardeen Batisse', avatar: 'https://i.pravatar.cc/80?img=3', email: 'abatisse2@nih.gov' },
    { value: 4, name: 'Graeme Yellowley', avatar: 'https://i.pravatar.cc/80?img=4', email: 'gyellowley3@behance.net' },
    { value: 5, name: 'Dido Wilford', avatar: 'https://i.pravatar.cc/80?img=5', email: 'dwilford4@jugem.jp' },
    { value: 6, name: 'Celesta Orwin', avatar: 'https://i.pravatar.cc/80?img=6', email: 'corwin5@meetup.com' },
    { value: 7, name: 'Sally Main', avatar: 'https://i.pravatar.cc/80?img=7', email: 'smain6@techcrunch.com' },
    { value: 8, name: 'Grethel Haysman', avatar: 'https://i.pravatar.cc/80?img=8', email: 'ghaysman7@mashable.com' },
    {
      value: 9,
      name: 'Marvin Mandrake',
      avatar: 'https://i.pravatar.cc/80?img=9',
      email: 'mmandrake8@sourceforge.net'
    },
    { value: 10, name: 'Corrie Tidey', avatar: 'https://i.pravatar.cc/80?img=10', email: 'ctidey9@youtube.com' }
  ];

  function tagTemplate(tagData) {
    return `
    <tag title="${tagData.title || tagData.email}"
      contenteditable='false'
      spellcheck='false'
      tabIndex="-1"
      class="${this.settings.classNames.tag} ${tagData.class || ''}"
      ${this.getAttributes(tagData)}
    >
      <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
      <div>
        <div class='tagify__tag__avatar-wrap'>
          <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
        </div>
        <span class='tagify__tag-text'>${tagData.name}</span>
      </div>
    </tag>
  `;
  }

  function suggestionItemTemplate(tagData) {
    return `
    <div ${this.getAttributes(tagData)}
      class='tagify__dropdown__item align-items-center ${tagData.class || ''}'
      tabindex="0"
      role="option"
    >
      ${
        tagData.avatar
          ? `<div class='tagify__dropdown__item__avatar-wrap'>
        <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
      </div>`
          : ''
      }
      <div class="fw-medium">${tagData.name}</div>
      <span>${tagData.email}</span>
    </div>
  `;
  }

  function dropdownHeaderTemplate(suggestions) {
    return `
        <div class="${this.settings.classNames.dropdownItem} ${this.settings.classNames.dropdownItem}__addAll">
            <strong>${this.value.length ? 'Add remaining' : 'Add All'}</strong>
            <span>${suggestions.length} members</span>
        </div>
    `;
  }

  if (tagifyUserListEl) {
    const tagifyUserList = new Tagify(tagifyUserListEl, {
      tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
      enforceWhitelist: true,
      skipInvalid: true, // do not remporarily add invalid tags
      dropdown: {
        closeOnSelect: false,
        enabled: 0,
        classname: 'users-list',
        searchKeys: ['name', 'email'] // very important to set by which keys to search for suggesttions when typing
      },
      templates: {
        tag: tagTemplate,
        dropdownItem: suggestionItemTemplate,
        dropdownHeader: dropdownHeaderTemplate
      },
      whitelist: usersList
    });

    tagifyUserList.on('dropdown:select', onSelectSuggestion).on('edit:start', onEditStart); // show custom text in the tag while in edit-mode

    function onSelectSuggestion(e) {
      // custom class from "dropdownHeaderTemplate"
      if (e.detail.elm.classList.contains(`${tagifyUserList.settings.classNames.dropdownItem}__addAll`)) {
        tagifyUserList.dropdown.selectAll();
      }
    }

    function onEditStart({ detail: { tag, data } }) {
      tagifyUserList.setTagTextNode(tag, `${data.name} <${data.email}>`);
    }
  }

  // Email List suggestion
  const tagifyEmailListEl = document.querySelector('#TagifyEmailList');
  if (tagifyEmailListEl) {
    const randomStringsArr = Array.from({ length: 100 }, () => {
      return (
        Array.from({ length: Math.floor(Math.random() * 10 + 3) }, () =>
          String.fromCharCode(Math.random() * (123 - 97) + 97)
        ).join('') + '@gmail.com'
      );
    });

    const tagifyEmailList = new Tagify(tagifyEmailListEl, {
      pattern:
        /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
      whitelist: randomStringsArr,
      callbacks: {
        invalid: onInvalidTag
      },
      dropdown: {
        position: 'text',
        enabled: 1 // show suggestions dropdown after 1 typed character
      }
    });

    const button = tagifyEmailListEl.nextElementSibling;
    button.addEventListener('click', () => tagifyEmailList.addEmptyTag());

    function onInvalidTag(e) {
      console.log('invalid', e.detail);
    }
  }
})();
