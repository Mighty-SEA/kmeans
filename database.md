# Database Schema

This document outlines the database schema of the KMeans clustering system.

## Entity-Relationship Diagram

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email
        timestamp email_verified_at
        string password
        string avatar
        string remember_token
        timestamps created_at
        timestamps updated_at
    }

    penerima {
        bigint id PK
        string nama
        string alamat
        string no_hp
        int usia
        int jumlah_anak
        tinyint kelayakan_rumah
        decimal pendapatan_perbulan
        timestamps created_at
        timestamps updated_at
    }

    clustering_results {
        bigint id PK
        bigint penerima_id FK
        int cluster
        float silhouette
        timestamps created_at
        timestamps updated_at
    }

    jobs {
        bigint id PK
        string queue
        longText payload
        tinyint attempts
        integer reserved_at
        integer available_at
        integer created_at
    }

    job_batches {
        string id PK
        string name
        integer total_jobs
        integer pending_jobs
        integer failed_jobs
        longText failed_job_ids
        mediumText options
        integer cancelled_at
        integer created_at
        integer finished_at
    }

    failed_jobs {
        bigint id PK
        string uuid
        text connection
        text queue
        longText payload
        longText exception
        timestamp failed_at
    }

    clustering_results }|--|| penerima : "belongs to"
```

## Table Descriptions

### users
Standard user authentication table with basic profile information.

### penerima
Contains data about recipients with socioeconomic attributes used for clustering.

### clustering_results
Stores the results of K-means clustering algorithm applied to penerima data.

### Other Tables
The database also includes Laravel's standard tables for job processing.
