{
   "_id": "_design/identifier",
   "language": "javascript",
   "views": {
       "doi": {
           "map": "function(doc) { if (doc.identifier) {    for (var i in doc.identifier)    {      if (doc.identifier[i].type == \"doi\")      {        emit(doc.identifier[i].id, doc._id);      }    } }\n\n\n}"
       },
       "issn": {
           "map": "function(doc) { if (doc.journal) {   if (doc.journal.identifier)   {   for (var i in doc.journal.identifier)    {      if (doc.journal.identifier[i].type == \"issn\")      {        emit(doc.journal.identifier[i].id, doc._id);      }    }   } }\n}"
       },
       "pmid": {
           "map": "function(doc) { if (doc.identifier) {    for (var i in doc.identifier)    {      if (doc.identifier[i].type == \"pmid\")      {        emit(doc.identifier[i].id, doc._id);      }    } }\n\n\n}"
       },
       "jstor": {
           "map": "function(doc) { if (doc.identifier) {    for (var i in doc.identifier)    {      if (doc.identifier[i].type == \"jstor\")      {        emit(doc.identifier[i].id, doc._id);      }    } }\n\n\n}"
       },
       "cinii": {
           "map": "function(doc) { if (doc.identifier) {    for (var i in doc.identifier)    {      if (doc.identifier[i].type == \"cinii\")      {        emit(doc.identifier[i].id, doc._id);      }    } }\n\n\n}"
       },
       "handle": {
           "map": "function(doc) { if (doc.identifier) {    for (var i in doc.identifier)    {      if (doc.identifier[i].type == \"handle\")      {        emit(doc.identifier[i].id, doc._id);      }    } }\n\n\n}"
       },
       "pmc": {
           "map": "function(doc) { if (doc.identifier) {    for (var i in doc.identifier)    {      if (doc.identifier[i].type == \"pmc\")      {        emit(doc.identifier[i].id, doc._id);      }    } }\n\n\n}"
       }
   }
}