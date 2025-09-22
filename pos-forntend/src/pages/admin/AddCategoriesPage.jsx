import React, { useEffect, useState } from 'react'
import { getCategories, createCategory, updateCategory, deleteCategory } from '../../api/client.js'

export default function AddCategoriesPage() {
  const [categories, setCategories] = useState([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  const [newName, setNewName] = useState('')
  const [savingNew, setSavingNew] = useState(false)
  const [saveNewError, setSaveNewError] = useState(null)

  const [editingId, setEditingId] = useState(null)
  const [editName, setEditName] = useState('')
  const [savingEdit, setSavingEdit] = useState(false)

  const [deletingId, setDeletingId] = useState(null)

  const refresh = async () => {
    setLoading(true)
    setError(null)
    try {
      const data = await getCategories()
      setCategories(Array.isArray(data) ? data : [])
    } catch (e) {
      setError(e?.response?.data?.message || e?.message || 'Failed to load categories')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { refresh() }, [])

  const handleCreate = async (e) => {
    e?.preventDefault?.()
    if (!newName.trim()) {
      setSaveNewError('Name is required')
      return
    }
    setSavingNew(true)
    setSaveNewError(null)
    try {
      await createCategory({ name: newName.trim() })
      setNewName('')
      await refresh()
    } catch (e) {
      setSaveNewError(e?.response?.data?.message || e?.message || 'Failed to create category')
    } finally {
      setSavingNew(false)
    }
  }

  const startEdit = (cat) => {
    setEditingId(cat.id)
    setEditName(cat.name)
  }

  const cancelEdit = () => {
    setEditingId(null)
    setEditName('')
  }

  const handleUpdate = async (id) => {
    if (!editName.trim()) return alert('Name is required')
    setSavingEdit(true)
    try {
      await updateCategory(id, { name: editName.trim() })
      setEditingId(null)
      setEditName('')
      await refresh()
    } catch (e) {
      alert(e?.response?.data?.message || e?.message || 'Failed to update category')
    } finally {
      setSavingEdit(false)
    }
  }

  const handleDelete = async (id, name) => {
    if (!window.confirm(`Delete category "${name}"?`)) return
    setDeletingId(id)
    try {
      await deleteCategory(id)
      await refresh()
    } catch (e) {
      alert(e?.response?.data?.message || e?.message || 'Failed to delete category')
    } finally {
      setDeletingId(null)
    }
  }

  return (
    <div className="row">
      <div className="col-md-5 mb-3">
        <div className="card">
          <div className="card-header">Add Category</div>
          <div className="card-body">
            <form onSubmit={handleCreate}>
              <div className="form-group">
                <label>Name</label>
                <input
                  className="form-control"
                  value={newName}
                  onChange={(e) => setNewName(e.target.value)}
                  placeholder="e.g. Grocery"
                />
              </div>
              {saveNewError ? (
                <div className="alert alert-danger py-2">{saveNewError}</div>
              ) : null}
              <button type="submit" className="btn btn-primary" disabled={savingNew}>
                {savingNew ? 'Saving...' : 'Create'}
              </button>
            </form>
          </div>
        </div>
      </div>

      <div className="col-md-7 mb-3">
        <div className="card">
          <div className="card-header d-flex align-items-center">
            <span>Categories</span>
            <button className="btn btn-sm btn-outline-secondary ml-auto" onClick={refresh} disabled={loading}>
              {loading ? 'Refreshing...' : 'Refresh'}
            </button>
          </div>
          <div className="card-body">
            {error ? <div className="alert alert-danger py-2">{error}</div> : null}

            <div className="table-responsive">
              <table className="table table-sm table-bordered mb-0">
                <thead className="thead-light">
                  <tr>
                    <th style={{ width: 80 }}>ID</th>
                    <th>Name</th>
                    <th style={{ width: 160 }}>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {loading ? (
                    <tr>
                      <td colSpan={3}>Loading...</td>
                    </tr>
                  ) : categories.length === 0 ? (
                    <tr>
                      <td colSpan={3}>No categories found</td>
                    </tr>
                  ) : (
                    categories.map((c) => (
                      <tr key={c.id}>
                        <td>{c.id}</td>
                        <td>
                          {editingId === c.id ? (
                            <input
                              className="form-control form-control-sm"
                              value={editName}
                              onChange={(e) => setEditName(e.target.value)}
                              autoFocus
                            />
                          ) : (
                            c.name
                          )}
                        </td>
                        <td>
                          {editingId === c.id ? (
                            <>
                              <button
                                className="btn btn-sm btn-primary mr-2"
                                onClick={() => handleUpdate(c.id)}
                                disabled={savingEdit}
                              >
                                {savingEdit ? 'Saving...' : 'Save'}
                              </button>
                              <button className="btn btn-sm btn-secondary" onClick={cancelEdit} disabled={savingEdit}>
                                Cancel
                              </button>
                            </>
                          ) : (
                            <>
                              <button className="btn btn-sm btn-outline-info mr-2" onClick={() => startEdit(c)}>
                                Edit
                              </button>
                              <button
                                className="btn btn-sm btn-outline-danger"
                                onClick={() => handleDelete(c.id, c.name)}
                                disabled={deletingId === c.id}
                              >
                                {deletingId === c.id ? 'Deleting...' : 'Delete'}
                              </button>
                            </>
                          )}
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}