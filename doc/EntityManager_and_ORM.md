CarteBlanche - Entity Manager and Object Relational Mapper (ORM)
================================================================


## What's all this?

Any application needs to manage entities such as a user, a content or a lot of other objects.
This management is often handling with the help of a database but not always, like for the
sessions entities for example, that are defaultly managed in PHP without any help (in temporary files actually).
The object to manage entities no matter how is called an **Entity Manager**.

This *Entity Manager* may handle the collection of entities available in the application
and return when necessary a specific object for each entity-type. This specific object represents
the entity itself and is used to access or manipulate one or more rows of concerned entity-type.
This object is called an **Entity Repository**.

A second point is that the different entities may be dependent from each others. For instance,
a session is often attached to a user, whom can be the author of some contents, etc. These
dependencies are handled by an object called an **Object Relational Mapper** or **ORM**. 

These two concepts can't be considered without the help of the way to manage entities storage
such as a database as said above. This storage can be done in many ways and it is important
to be able to manage entities and relations without worring about it. The abstract layer
that handles this storage is an object called a **Storage Engine**.

This architecture is mostly inspired by the [Doctrine project](http://www.doctrine-project.org/).


## Verbose "what's all this?"

Basically, the *Entity Manager* MUST permits to get an object to manipulate a specific 
entity-type. This object can be considered as an *Entity Repository* which is entity-type
dependent. In this schema, the *Entity Manager* can be considered as a container of
*Entities Repositories*.

An *Entity Repository* MUST permits to read, create, modify or delete one or more 
entities that can be identified by their indexes which MUST be unique and constant
for a given entity, while the *ORM* MUST do the work of attachement or detachement of related 
entities cascading (or not) a modification on a parent to its children.

This is not the place to make a lesson about object relations but, as a reminder, there are
three basic types of relation between entities:

-   **one to one** : a given instance of entity A can be related to a given instance of entity B
-   **one to many** : a given instance of entity A can be related to many instances of entity B
-   **many to many** : many instances of entity A can be related to many instances of entity B

We MUST keep these relation types in mind while creating new entities definitions in CarteBlanche.

Finally, the *Storage Engine* MUST be capable to store an entity and retrieve it by its index
to allow all type of actions on it: read, create, update or delete. This is not mandatory but
it MAY also take some relational rules in charge by mapping them on entities indexes. When this
is the case, this feature MUST not mean that the *ORM* can leave this work to the storage engine.
In this schema, the *Entity Manager* can be considered as a container of *Storage Engines*.

These three *units of works*, the *entity manager*, the *object relational mapper* and the
*storage engine* MUST be independent from each others and MAY even ignore which driver is
running for the others.


## Usage

In CarteBlanche, a new project will start defining its storage engines through configuration
and the rules used by its ORM. After that, all the work will be to define some entities and
their relations.


## Schema & Example

    Entity Manager
    | ------------> acts like a container of repositories and storage engines
    | ------------> is NOT specific for an entity-type
    | ------------> allows to get the entity-type model & repository
    | ------------> allows to get the storage engine configured for the entity-type
    
    Object Relational Mapper
    | ------------> allows to access related entities repositories from the entity manager
    | ------------> allows to manipulate related entities

    Entity Repository
    | ------------> is specific for an entity-type
    | ------------> is related to a more specific entity-type model object
    | ------------> allows to access one or more entities reading them from the storage engine
    | ------------> allows to declare relational dependencies of the entity-type to the ORM
    | ------------> allows to manipulate entities (creation, modification and deletion)
    
    Entity Model
    | ------------> is very specific for an entity-type
    | ------------> allows to access all entity-type properties
    | ------------> allows to manage collections of related entity-types objects

    Storage Engine
    | ------------> allows to store entities values in a way to be able to retrieve them
    | ------------> uses index for each entity to retrieve it specifically

For instance, considering a classic user object assuming we store our users in a database
table and that a user can be related to a collection of contents that are stored in the
filesystem:

-   the entity-type is `user`
-   the storage engine for the `user` entity-type is `database` with a specific
    configuration
-   the entity-type `user` have a relation `one-to-many` with the entity-type `content`
-   the storage engine for the `content` entity-type is `filesystem` with a specific
    configuration

Saying we want to read the user entity with ID 1 who is the author of two contents with
ID 2 & 3:

-   we may be able to get the `UserRepository` from the `EntityManager`
-   the `UserRepository` may be able to load its required storage engine `Database`
    from the `EntityManager`
-   the `UserRepository` may declare the entity-type dependencies to the `ORM`
-   the `ORM` may be able to get the `ContentRepository` from the `EntityManager`
-   the `ContentRepository` may be able to load its required storage engine `filesystem`
    from the `EntityManager`
-   the `UserRepository` may allow us to read the `ID=1` user entity
-   the `ORM` may automatically attach the `ID=2` and `ID=3` content entities to the user
-   the `ContentRepository` may allow user object to access the `ID=2` and `ID=3` content entities
-   we may finally have a `User` model class instance loaded with database informations
    of the row of our `user` table where primary index is `1` and with a collection of
    contents loaded as some `Content` model instances loaded themselves with files
    contents where the primary indexes are `2` and `3`


----
**(c) 2013-2015 [Les Ateliers Pierrot](http://www.ateliers-pierrot.fr/)** - Paris, France - Some rights reserved.

This documentation is licensed under the [Creative Commons - Attribution - Share Alike - Unported - version 3.0](http://creativecommons.org/licenses/by-sa/3.0/) license.
