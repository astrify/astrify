import axios from 'axios'

export const state = {
  accounts: [],
  linkedOrgs: [],
  unlinkedOrgs: []
}

// getters
export const getters = {
  getAccountByUuid: (state) => (uuid) => {
    return state.accounts.find(account => account.uuid === uuid)
  },
  getAccountBySlug: (state) => (slug) => {
    return state.accounts.find(account => account.slug === slug)
  },
  accounts: state => (state.accounts.length) ? state.accounts : null,
  linkedOrgs: state => (state.linkedOrgs.length) ? state.linkedOrgs : null,
  unlinkedOrgs: state => (state.unlinkedOrgs.length) ? state.unlinkedOrgs : null
}

// actions
export const actions = {
  async fetchAccounts ({ commit }) {
    const { data } = await axios.get('/api/accounts')
    commit('SET_ACCOUNTS', { accounts: data.data })
  },

  async fetchAccount ({ commit, getters }, uuid) {
    let account = getters.getAccountByUuid(uuid)

    if (!account) {
      const { data } = await axios.get('/api/accounts/' + uuid)
      commit('SET_ACCOUNT', { account: data.data })
    }
  },

  async fetchLinkedOrgs ({ commit }, uuid) {
    const { data } = await axios.get('/api/organizations', { params: { 'linked_account_uuid': uuid } })
    commit('SET_LINKED_ORGS', { orgs: data.data })
  },

  async fetchUnlinkedOrgs ({ commit }, uuid) {
    const { data } = await axios.get('/api/organizations', { params: { 'unlinked_account_uuid': uuid } })
    commit('SET_UNLINKED_ORGS', { orgs: data.data })
  }
}

// mutations
export const mutations = {
  SET_ACCOUNTS (state, { accounts }) {
    state.accounts = accounts
  },
  SET_ACCOUNT (state, { account }) {
    state.accounts = [
      ...state.accounts.filter(element => element.uuid !== account.uuid),
      account
    ]
  },
  SET_LINKED_ORGS (state, { orgs }) {
    state.linkedOrgs = orgs
  },
  SET_UNLINKED_ORGS (state, { orgs }) {
    state.unlinkedOrgs = orgs
  }
}
